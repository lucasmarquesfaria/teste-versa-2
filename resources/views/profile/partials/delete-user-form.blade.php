<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">{{ __('Excluir Conta') }}</h2>
        <p class="mt-1 text-sm text-gray-600">{{ __('Após excluir sua conta, todos os seus dados serão apagados permanentemente.') }}</p>
    </header>

    <form method="post" action="{{ route('profile.destroy') }}" class="mt-6 space-y-6">
        @csrf
        @method('delete')

        <x-danger-button>{{ __('Excluir Conta') }}</x-danger-button>
    </form>
</section>
