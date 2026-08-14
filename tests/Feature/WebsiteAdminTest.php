<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WebsiteAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function createUser(string $roleSlug): User
    {
        $role = Role::create(['name' => ucwords(str_replace('-', ' ', $roleSlug)), 'slug' => $roleSlug]);

        return User::factory()->create(['role_id' => $role->id]);
    }

    protected function makePost(User $author, array $overrides = []): Post
    {
        return Post::create(array_merge([
            'title' => 'Berita Contoh',
            'slug' => 'berita-contoh',
            'excerpt' => 'Ringkasan singkat berita contoh.',
            'body' => 'Isi lengkap berita contoh.',
            'status' => 'published',
            'author_id' => $author->id,
            'published_at' => now(),
        ], $overrides));
    }

    public function test_website_login_page_is_public(): void
    {
        $this->get(route('website.login'))
            ->assertOk()
            ->assertSee('Kelola Website')
            ->assertSee('Login Admin');
    }

    public function test_admin_can_login_via_website_login(): void
    {
        $user = $this->createUser('admin');

        $this->post(route('website.login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('website.posts.index'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_non_admin_cannot_login_via_website_login(): void
    {
        $user = $this->createUser('staff');

        $this->post(route('website.login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect()->assertSessionHas('error', 'Akun tidak memiliki akses pengelolaan website.');

        $this->assertGuest();
    }

    public function test_dashboard_login_still_works_for_admin(): void
    {
        $user = $this->createUser('admin');

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_non_admin_cannot_access_post_routes(): void
    {
        $user = $this->createUser('staff');

        $this->actingAs($user)
            ->get(route('website.posts.index'))
            ->assertForbidden();
    }

    public function test_admin_can_manage_posts(): void
    {
        $user = $this->createUser('admin');
        $this->actingAs($user);

        $this->get(route('website.posts.create'))->assertOk();

        $this->post(route('website.posts.store'), [
            'title' => 'Pengumuman Layanan Baru',
            'excerpt' => 'Ringkasan singkat pengumuman.',
            'body' => 'Isi lengkap pengumuman layanan baru.',
            'status' => 'published',
        ])->assertRedirect(route('website.posts.index'));

        $this->assertDatabaseHas('posts', [
            'title' => 'Pengumuman Layanan Baru',
            'slug' => 'pengumuman-layanan-baru',
            'status' => 'published',
            'author_id' => $user->id,
        ]);

        $post = Post::where('slug', 'pengumuman-layanan-baru')->firstOrFail();
        $this->assertNotNull($post->published_at);

        $this->put(route('website.posts.update', $post), [
            'title' => 'Pengumuman Layanan Baru (Revisi)',
            'excerpt' => 'Ringkasan revisi.',
            'body' => 'Isi revisi.',
            'status' => 'draft',
        ])->assertRedirect(route('website.posts.index'));

        $post->refresh();
        $this->assertSame('draft', $post->status);
        $this->assertNull($post->published_at);

        $this->delete(route('website.posts.destroy', $post))
            ->assertRedirect(route('website.posts.index'));

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_slug_is_unique_across_posts(): void
    {
        $user = $this->createUser('admin');
        $this->actingAs($user);

        $payload = [
            'title' => 'Berita Khusus',
            'excerpt' => 'Ringkasan.',
            'body' => 'Isi.',
            'status' => 'draft',
        ];

        $this->post(route('website.posts.store'), $payload)->assertRedirect();
        $this->post(route('website.posts.store'), $payload)->assertRedirect();

        $this->assertSame(2, Post::where('title', 'Berita Khusus')->count());
        $this->assertTrue(Post::where('slug', 'berita-khusus-2')->exists());
    }

    public function test_cover_image_is_uploaded_to_public_disk(): void
    {
        Storage::fake('public');
        $user = $this->createUser('admin');
        $this->actingAs($user);

        $this->post(route('website.posts.store'), [
            'title' => 'Berita Dengan Gambar',
            'excerpt' => 'Ringkasan.',
            'body' => 'Isi.',
            'status' => 'published',
            'cover_image' => UploadedFile::fake()->image('cover.jpg'),
        ])->assertRedirect(route('website.posts.index'));

        $post = Post::where('slug', 'berita-dengan-gambar')->firstOrFail();
        Storage::disk('public')->assertExists($post->cover_image);
    }

    public function test_public_berita_only_shows_published_posts(): void
    {
        $author = $this->createUser('admin');

        $this->makePost($author, [
            'title' => 'Berita Publik',
            'slug' => 'berita-publik',
        ]);
        $this->makePost($author, [
            'title' => 'Draft Internal',
            'slug' => 'draft-internal',
            'status' => 'draft',
            'published_at' => null,
        ]);

        $this->get(route('berita'))
            ->assertOk()
            ->assertSee('Berita Publik')
            ->assertDontSee('Draft Internal');

        $this->get(route('berita.show', 'berita-publik'))
            ->assertOk()
            ->assertSee('Berita Publik');

        $this->get(route('berita.show', 'draft-internal'))
            ->assertNotFound();
    }

    public function test_public_home_shows_latest_published_posts(): void
    {
        $author = $this->createUser('admin');

        $this->makePost($author, [
            'title' => 'Sambutan Direktur',
            'slug' => 'sambutan-direktur',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Sambutan Direktur')
            ->assertSee('Berita Terbaru');
    }
}
