<?php

use App\Mail\EmailReminder;
use App\Mail\StrongerEmailReminder;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

// ─── EmailReminder (friendly reminder) ───────────────────────────────────────

test('friendly reminder email is sent to the correct user', function () {
    Mail::fake();

    $user = User::factory()->create();

    Mail::to($user->email)->send(new EmailReminder($user->name));

    Mail::assertSent(EmailReminder::class, fn ($mail) => $mail->hasTo($user->email));
});

test('friendly reminder email has the correct subject', function () {
    Mail::fake();

    $user = User::factory()->create();

    Mail::to($user->email)->send(new EmailReminder($user->name));

    Mail::assertSent(
        EmailReminder::class,
        fn ($mail) => $mail->hasSubject('Friendly Reminder: Please Update Your Project Progress')
    );
});

// ─── StrongerEmailReminder (firm warning) ─────────────────────────────────────

test('warning email is sent to all project members', function () {
    Mail::fake();

    $members = User::factory()->count(3)->create();

    Mail::to($members)->send(new StrongerEmailReminder('Test Project'));

    Mail::assertSent(StrongerEmailReminder::class, 1);

    Mail::assertSent(StrongerEmailReminder::class, function ($mail) use ($members) {
        return $mail->hasTo($members[0]->email)
            && $mail->hasTo($members[1]->email)
            && $mail->hasTo($members[2]->email);
    });
});

test('warning email has the correct subject', function () {
    Mail::fake();

    $user = User::factory()->create();

    Mail::to($user->email)->send(new StrongerEmailReminder('Test Project'));

    Mail::assertSent(
        StrongerEmailReminder::class,
        fn ($mail) => $mail->hasSubject('Urgent: Immediate Action Required — Project Updates Overdue')
    );
});
