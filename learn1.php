<?php
$user = [
    'name' => 'nishanth',
    'topic' => 'apis'
];

function welcomeUser(array $user): string
{
    return "Welcome {$user['name']}! You are learning {$user['topic']}\n";
}

echo welcomeUser($user);

foreach ($user as $key => $value) {
    echo "$key and $value\n";
}

print_r(array_keys($user));
print_r(array_values($user));