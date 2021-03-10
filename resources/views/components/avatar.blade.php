@props(['user' => user()])

<img {{ $attributes->merge([
    'class' => 'rounded-full object-cover',
    'src' => $user->avatarUrl,
    'alt' => __('alts.avatar', ['name' => $user->name]),
]) }} />
