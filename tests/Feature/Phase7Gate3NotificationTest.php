<?php

namespace Tests\Feature;

use App\Constants\NotificationType;
use App\Models\Notification;
use App\Models\User;
use App\Policies\NotificationPolicy;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;
use Tests\TestCase;

class Phase7Gate3NotificationTest extends TestCase
{
    use RefreshDatabase;

    protected NotificationService $notificationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->notificationService = app(NotificationService::class);
    }

    protected function createUser(array $overrides = []): User
    {
        return User::factory()->create($overrides);
    }

    // ==========================================
    // 1. CREATION TESTS
    // ==========================================

    /** @test */
    public function test_1_server_can_create_notification_for_intended_recipient()
    {
        $user = $this->createUser();

        $notification = $this->notificationService->send(
            $user,
            NotificationType::MEMBER_VERIFICATION_APPROVED,
            'Keanggotaan Disetujui',
            'Selamat! Pengajuan keanggotaan Anda telah disetujui.',
            ['member_id' => 10]
        );

        $this->assertInstanceOf(Notification::class, $notification);
        $this->assertEquals($user->id, $notification->user_id);
        $this->assertEquals(NotificationType::MEMBER_VERIFICATION_APPROVED, $notification->type);
        $this->assertEquals('Keanggotaan Disetujui', $notification->title);
        $this->assertEquals('Selamat! Pengajuan keanggotaan Anda telah disetujui.', $notification->message);
        $this->assertEquals(['member_id' => 10], $notification->data);
        $this->assertFalse($notification->is_read);
        $this->assertNull($notification->read_at);

        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'user_id' => $user->id,
            'type' => NotificationType::MEMBER_VERIFICATION_APPROVED,
            'is_read' => false,
        ]);
    }

    /** @test */
    public function test_2_notification_creation_with_user_id_integer_and_invalid_recipient_validation()
    {
        $user = $this->createUser();

        $notification = $this->notificationService->send(
            $user->id,
            NotificationType::LOAN_APPROVED,
            'Pinjaman Disetujui',
            'Pinjaman Anda telah disetujui oleh admin.',
            ['loan_id' => 45]
        );

        $this->assertEquals($user->id, $notification->user_id);

        // Negative recipient ID
        $this->expectException(InvalidArgumentException::class);
        $this->notificationService->send(
            -1,
            NotificationType::LOAN_APPROVED,
            'Judul',
            'Pesan'
        );
    }

    /** @test */
    public function test_3_notification_send_many_dispatches_to_multiple_recipients()
    {
        $user1 = $this->createUser();
        $user2 = $this->createUser();
        $user3 = $this->createUser();

        $notifications = $this->notificationService->sendMany(
            [$user1, $user2, $user3],
            NotificationType::SYSTEM_ALERT,
            'Pemeliharaan Sistem',
            'Sistem akan dilakukan pemeliharaan berkala.',
            ['scheduled_at' => '2026-10-15 00:00:00']
        );

        $this->assertCount(3, $notifications);
        $this->assertEquals($user1->id, $notifications[0]->user_id);
        $this->assertEquals($user2->id, $notifications[1]->user_id);
        $this->assertEquals($user3->id, $notifications[2]->user_id);

        $this->assertEquals(1, $user1->notifications()->count());
        $this->assertEquals(1, $user2->notifications()->count());
        $this->assertEquals(1, $user3->notifications()->count());
    }

    // ==========================================
    // 2. RECIPIENT ISOLATION TESTS
    // ==========================================

    /** @test */
    public function test_4_user_can_retrieve_own_notifications_and_cannot_retrieve_others()
    {
        $userA = $this->createUser();
        $userB = $this->createUser();

        $this->notificationService->send($userA, NotificationType::LOAN_APPROVED, 'Notif A', 'Pesan A');
        $this->notificationService->send($userB, NotificationType::LOAN_REJECTED, 'Notif B', 'Pesan B');

        $userANotifs = $this->notificationService->getUserNotifications($userA);
        $this->assertEquals(1, $userANotifs->total());
        $this->assertEquals('Notif A', $userANotifs->first()->title);

        $userBNotifs = $this->notificationService->getUserNotifications($userB);
        $this->assertEquals(1, $userBNotifs->total());
        $this->assertEquals('Notif B', $userBNotifs->first()->title);

        // Verification via user relationship
        $this->assertCount(1, $userA->notifications);
        $this->assertEquals('Notif A', $userA->notifications->first()->title);
        $this->assertCount(1, $userB->notifications);
        $this->assertEquals('Notif B', $userB->notifications->first()->title);
    }

    /** @test */
    public function test_5_guest_cannot_access_notification_endpoints()
    {
        $user = $this->createUser();
        $notification = $this->notificationService->send($user, NotificationType::LOAN_APPROVED, 'Notif', 'Pesan');

        // Guest attempting mark as read
        $response = $this->post(route('notifications.read', $notification->id));
        $response->assertRedirect(route('login'));

        // Guest attempting mark all read
        $responseAll = $this->post(route('notifications.read_all'));
        $responseAll->assertRedirect(route('login'));
    }

    // ==========================================
    // 3. READ STATE FOUNDATION TESTS
    // ==========================================

    /** @test */
    public function test_6_read_and_unread_lifecycle_and_unread_counters()
    {
        $user = $this->createUser();

        $this->assertEquals(0, $this->notificationService->getUnreadCount($user));

        $notif1 = $this->notificationService->send($user, NotificationType::LOAN_APPROVED, 'N1', 'M1');
        $notif2 = $this->notificationService->send($user, NotificationType::SAVINGS_PAYMENT_APPROVED, 'N2', 'M2');

        $this->assertEquals(2, $this->notificationService->getUnreadCount($user));
        $this->assertEquals(2, $user->unreadNotifications()->count());

        // Mark single as read
        $this->notificationService->markAsRead($notif1, $user);
        $notif1->refresh();

        $this->assertTrue($notif1->is_read);
        $this->assertNotNull($notif1->read_at);
        $this->assertEquals(1, $this->notificationService->getUnreadCount($user));

        // Scopes test
        $this->assertEquals(1, Notification::where('user_id', $user->id)->unread()->count());
        $this->assertEquals(1, Notification::where('user_id', $user->id)->read()->count());

        // Mark all as read
        $updatedCount = $this->notificationService->markAllAsRead($user);
        $this->assertEquals(1, $updatedCount);
        $this->assertEquals(0, $this->notificationService->getUnreadCount($user));
        $this->assertEquals(2, Notification::where('user_id', $user->id)->read()->count());
    }

    /** @test */
    public function test_7_authenticated_user_can_mark_own_notification_as_read_via_http()
    {
        $user = $this->createUser();
        $notification = $this->notificationService->send($user, NotificationType::LOAN_APPROVED, 'Title', 'Msg');

        $this->actingAs($user);
        $response = $this->post(route('notifications.read', $notification->id));

        $response->assertSessionHas('success');
        $notification->refresh();
        $this->assertTrue($notification->is_read);
        $this->assertNotNull($notification->read_at);
    }

    /** @test */
    public function test_8_user_cannot_mark_another_users_notification_as_read_via_http()
    {
        $userA = $this->createUser();
        $userB = $this->createUser();
        $notificationB = $this->notificationService->send($userB, NotificationType::LOAN_APPROVED, 'Title B', 'Msg B');

        // User A tries to mark User B's notification
        $this->actingAs($userA);
        $response = $this->post(route('notifications.read', $notificationB->id));

        $response->assertStatus(403);
        $notificationB->refresh();
        $this->assertFalse($notificationB->is_read);
    }

    /** @test */
    public function test_9_service_throws_exception_if_unauthorized_user_attempts_mark_as_read()
    {
        $userA = $this->createUser();
        $userB = $this->createUser();
        $notificationB = $this->notificationService->send($userB, NotificationType::LOAN_APPROVED, 'Title B', 'Msg B');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unauthorized notification access');

        $this->notificationService->markAsRead($notificationB, $userA);
    }

    // ==========================================
    // 4. MUTATION PROTECTION & IMMUTABILITY TESTS
    // ==========================================

    /** @test */
    public function test_10_notification_content_is_immutable_and_cannot_be_altered()
    {
        $user = $this->createUser();
        $notification = $this->notificationService->send(
            $user,
            NotificationType::LOAN_APPROVED,
            'Original Title',
            'Original Message',
            ['loan_id' => 1]
        );

        // Attempt to alter title
        try {
            $notification->update(['title' => 'Tampered Title']);
            $this->fail('Altering notification title should throw RuntimeException.');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('Notification content is immutable', $e->getMessage());
        }

        // Attempt to alter recipient
        $anotherUser = $this->createUser();
        try {
            $notification->update(['user_id' => $anotherUser->id]);
            $this->fail('Altering notification recipient should throw RuntimeException.');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('Notification content is immutable', $e->getMessage());
        }

        // Attempt to alter payload
        try {
            $notification->update(['data' => ['loan_id' => 999]]);
            $this->fail('Altering notification payload should throw RuntimeException.');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('Notification content is immutable', $e->getMessage());
        }

        // Verify notification remains completely unmodified
        $notification->refresh();
        $this->assertEquals('Original Title', $notification->title);
        $this->assertEquals($user->id, $notification->user_id);
        $this->assertEquals(['loan_id' => 1], $notification->data);
    }

    /** @test */
    public function test_11_no_http_routes_exist_to_arbitrarily_create_or_modify_notifications()
    {
        $user = $this->createUser();
        $this->actingAs($user);

        // No POST /notifications to create arbitrary notification
        $responsePost = $this->post('/notifications', [
            'title' => 'Fake Notification',
            'message' => 'Spoofed',
            'user_id' => $user->id,
        ]);
        $this->assertTrue(in_array($responsePost->getStatusCode(), [404, 405]));

        // No PUT /notifications/{id} to modify notification
        $notification = $this->notificationService->send($user, NotificationType::LOAN_APPROVED, 'Title', 'Msg');
        $responsePut = $this->put("/notifications/{$notification->id}", [
            'title' => 'Tampered Title',
        ]);
        $this->assertTrue(in_array($responsePut->getStatusCode(), [404, 405]));
    }

    /** @test */
    public function test_12_policy_strictly_isolates_view_update_and_delete()
    {
        $userA = $this->createUser();
        $userB = $this->createUser();
        $notificationA = $this->notificationService->send($userA, NotificationType::LOAN_APPROVED, 'A', 'A');

        $policy = new NotificationPolicy();

        // User A owns notification A
        $this->assertTrue($policy->view($userA, $notificationA));
        $this->assertTrue($policy->update($userA, $notificationA));
        $this->assertTrue($policy->delete($userA, $notificationA));

        // User B does NOT own notification A
        $this->assertFalse($policy->view($userB, $notificationA));
        $this->assertFalse($policy->update($userB, $notificationA));
        $this->assertFalse($policy->delete($userB, $notificationA));
    }

    // ==========================================
    // 5. SECURITY & PAYLOAD SANITIZATION TESTS
    // ==========================================

    /** @test */
    public function test_13_sensitive_credentials_and_pii_are_scrubbed_from_payload()
    {
        $user = $this->createUser();

        $dirtyPayload = [
            'loan_id' => 101,
            'reference_number' => 'REF-SAFE-001',
            'password' => 'supersecret',
            'password_confirmation' => 'supersecret',
            'token' => 'bearer_123',
            'access_token' => 'access_xyz',
            'authorization' => 'Bearer token',
            'session_id' => 'sess_123',
            'credential' => 'admin_cred',
            'nik' => '3201010101010001',
            'ktp' => 'base64ktpdata',
            'file_ktp' => '/uploads/ktp.png',
            'slip_gaji' => '/uploads/slip.pdf',
            'bukti_transfer' => '/uploads/transfer.jpg',
            'bank_account' => '1234567890',
            'no_rek' => '987654321',
            'cvv' => '999',
            'pin' => '1234',
            'auth_token' => 'custom_token',
            'api_secret' => 'custom_secret',
            'nested' => [
                'safe_detail' => 'Detail Transaksi',
                'secret' => 'nested_secret',
                'password' => 'nested_pass',
                'deep' => [
                    'safe_item' => 'OK',
                    'card_number' => '4111222233334444',
                ],
            ],
        ];

        $notification = $this->notificationService->send(
            $user,
            NotificationType::LOAN_APPROVED,
            'Pinjaman Disetujui',
            'Pinjaman Anda telah disetujui.',
            $dirtyPayload
        );

        $cleanData = $notification->data;

        // Safe values retained
        $this->assertEquals(101, $cleanData['loan_id']);
        $this->assertEquals('REF-SAFE-001', $cleanData['reference_number']);
        $this->assertEquals('Detail Transaksi', $cleanData['nested']['safe_detail']);
        $this->assertEquals('OK', $cleanData['nested']['deep']['safe_item']);

        // Sensitive top-level stripped
        $this->assertArrayNotHasKey('password', $cleanData);
        $this->assertArrayNotHasKey('password_confirmation', $cleanData);
        $this->assertArrayNotHasKey('token', $cleanData);
        $this->assertArrayNotHasKey('access_token', $cleanData);
        $this->assertArrayNotHasKey('authorization', $cleanData);
        $this->assertArrayNotHasKey('session_id', $cleanData);
        $this->assertArrayNotHasKey('credential', $cleanData);
        $this->assertArrayNotHasKey('nik', $cleanData);
        $this->assertArrayNotHasKey('ktp', $cleanData);
        $this->assertArrayNotHasKey('file_ktp', $cleanData);
        $this->assertArrayNotHasKey('slip_gaji', $cleanData);
        $this->assertArrayNotHasKey('bukti_transfer', $cleanData);
        $this->assertArrayNotHasKey('bank_account', $cleanData);
        $this->assertArrayNotHasKey('no_rek', $cleanData);
        $this->assertArrayNotHasKey('cvv', $cleanData);
        $this->assertArrayNotHasKey('pin', $cleanData);
        $this->assertArrayNotHasKey('auth_token', $cleanData);
        $this->assertArrayNotHasKey('api_secret', $cleanData);

        // Sensitive nested stripped
        $this->assertArrayNotHasKey('secret', $cleanData['nested']);
        $this->assertArrayNotHasKey('password', $cleanData['nested']);
        $this->assertArrayNotHasKey('card_number', $cleanData['nested']['deep']);
    }

    // ==========================================
    // 6. TRANSACTION SAFETY TESTS
    // ==========================================

    /** @test */
    public function test_14_successful_business_transaction_persists_notification()
    {
        $user = $this->createUser();

        DB::transaction(function () use ($user) {
            $this->notificationService->send(
                $user,
                NotificationType::LOAN_APPROVED,
                'Transaksi Sukses',
                'Operasi bisnis berhasil.'
            );
        });

        $this->assertEquals(1, Notification::where('user_id', $user->id)->count());
    }

    /** @test */
    public function test_15_rolled_back_business_transaction_leaves_no_false_success_notification()
    {
        $user = $this->createUser();
        $initialCount = Notification::count();

        try {
            DB::transaction(function () use ($user) {
                $this->notificationService->send(
                    $user,
                    NotificationType::LOAN_APPROVED,
                    'Transaksi Gagal',
                    'Operasi ini akan gagal.'
                );

                // Force downstream business error
                throw new \Exception('Simulated downstream business processing failure.');
            });
            $this->fail('Transaction should have rolled back.');
        } catch (\Exception $e) {
            $this->assertEquals('Simulated downstream business processing failure.', $e->getMessage());
        }

        // Notification MUST NOT remain in database
        $this->assertEquals($initialCount, Notification::count());
        $this->assertDatabaseMissing('notifications', [
            'user_id' => $user->id,
            'title' => 'Transaksi Gagal',
        ]);
    }
}
