<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Post;
use App\Models\Role;
use App\Models\Service;
use App\Models\Setting;
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

    public function test_authenticated_admin_visiting_website_login_goes_to_posts(): void
    {
        $user = $this->createUser('admin');

        $this->actingAs($user)
            ->get(route('website.login'))
            ->assertRedirect(route('website.posts.index'));
    }

    public function test_authenticated_non_admin_visiting_website_login_goes_to_dashboard(): void
    {
        $user = $this->createUser('staff');

        $this->actingAs($user)
            ->get(route('website.login'))
            ->assertRedirect(route('dashboard'));
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

    public function test_settings_page_requires_admin_role(): void
    {
        $this->get(route('website.settings.index'))
            ->assertRedirect(route('login'));

        $staff = $this->createUser('staff');

        $this->actingAs($staff)
            ->get(route('website.settings.index'))
            ->assertForbidden();
    }

    public function test_admin_can_access_settings_page(): void
    {
        $admin = $this->createUser('admin');

        $this->actingAs($admin)
            ->get(route('website.settings.index'))
            ->assertOk()
            ->assertSee('Pengaturan Website')
            ->assertSee('Identitas')
            ->assertSee('Kartu Layanan')
            ->assertSee('Our Loyal Customers');
    }

    public function test_logo_can_be_updated_from_settings(): void
    {
        Storage::fake('public');
        $admin = $this->createUser('admin');

        $this->actingAs($admin)
            ->post(route('website.settings.logo'), [
                'logo' => UploadedFile::fake()->image('logo.png'),
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $path = Setting::get('site.logo');
        $this->assertNotNull($path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_services_can_be_updated_from_settings(): void
    {
        $admin = $this->createUser('admin');
        $service = Service::create([
            'slug' => 'ltl',
            'section' => 'layanan',
            'name' => 'Less Than Truckload',
            'badge' => 'Retail',
            'description' => 'Deskripsi lama.',
            'icon_svg' => 'M20 7l-8-4-8 4',
            'sort_order' => 1,
        ]);

        $this->actingAs($admin)
            ->post(route('website.settings.services'), [
                'services' => [
                    $service->id => [
                        'id' => $service->id,
                        'name' => 'LTL Edisi Baru',
                        'badge' => 'Retail Plus',
                        'description' => 'Deskripsi baru.',
                    ],
                ],
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $service->refresh();
        $this->assertSame('LTL Edisi Baru', $service->name);
        $this->assertSame('Retail Plus', $service->badge);
        $this->assertSame('Deskripsi baru.', $service->description);
    }

    public function test_client_logos_can_be_added_reordered_toggled_and_deleted(): void
    {
        Storage::fake('public');
        $admin = $this->createUser('admin');
        $this->actingAs($admin);

        $this->post(route('website.settings.clients.store'), [
            'logos' => [
                UploadedFile::fake()->image('klien-pertama.png'),
                UploadedFile::fake()->image('klien-kedua.png'),
            ],
        ])->assertRedirect()->assertSessionHas('success');

        $this->assertSame(2, Client::count());

        $first = Client::orderBy('sort_order')->first();
        $second = Client::orderBy('sort_order')->skip(1)->first();
        $this->assertTrue($first->sort_order < $second->sort_order);

        $this->post(route('website.settings.clients.move', $first), ['direction' => 'down'])
            ->assertRedirect();

        $first->refresh();
        $second->refresh();
        $this->assertTrue($first->sort_order > $second->sort_order);

        $this->patch(route('website.settings.clients.toggle', $first))
            ->assertRedirect();
        $first->refresh();
        $this->assertFalse($first->is_active);

        $this->delete(route('website.settings.clients.destroy', $first))
            ->assertRedirect();
        $this->assertSame(1, Client::count());
    }

    public function test_public_home_renders_services_and_clients_from_database(): void
    {
        $admin = $this->createUser('admin');

        Service::create([
            'slug' => 'ltl',
            'section' => 'layanan',
            'name' => 'Less Than Truckload (LTL)',
            'badge' => 'Retail',
            'description' => 'Deskripsi LTL.',
            'icon_svg' => 'M20 7l-8-4-8 4',
            'sort_order' => 1,
        ]);

        Client::create([
            'name' => 'PT Contoh Mitra',
            'image_path' => 'uploads/clients/mitra.png',
            'sort_order' => 1,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Less Than Truckload (LTL)')
            ->assertSee('PT Contoh Mitra');

        $this->get(route('services'))
            ->assertOk()
            ->assertSee('Less Than Truckload (LTL)');
    }
}
