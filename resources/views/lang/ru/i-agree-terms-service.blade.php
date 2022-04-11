{{--TODO: If I ever want to actually have a RU version I will have to translate the Terms of Service and Privacy Policy as well,
          and make sure they're shown correctly. --}}
<span>
    Я принимаю
    <x-link href="{{ config('app.terms_url') }}" :external="true" :icon="false">Пользовательское соглашение</x-link>
    и даю согласие на обработку персональных данных согласно
    <x-link href="{{ config('app.privacy_policy_url') }}" :external="true" :icon="false">Политики конфиденциальности.</x-link>
</span>
