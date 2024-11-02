@extends('layouts.app')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gradient-to-r from-blue-500 to-purple-600">
    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold text-center text-gray-800">Registro de Usuário</h2>
        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">
                    <span class="mr-2">👤 Nome *</span>
                </label>
                <input type="text" id="name" name="name" required
                       class="block w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                       placeholder="Digite seu nome">
                @error('email')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">
                <span class="mr-2">📱 Celular *</span>
                </label>
                <input type="tel" id="phone" name="phone" required
                       class="block w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                       placeholder="Digite seu número de celular">
                @error('phone')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">
                    <span class="mr-2">✉️ Email *</span>
                </label>
                <input type="email" id="email" name="email" required
                       class="block w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                       placeholder="Digite seu email">
                @error('email')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">
                    <span class="mr-2">🔒 Senha *</span>
                </label>
                <input type="password" id="password" name="password" required
                       class="block w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                       placeholder="Digite sua senha">
                @error('password')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                    <span class="mr-2">🔒 Confirmar Senha *</span>
                </label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                    class="block w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Digite sua senha novamente">
                @error('password_confirmation')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
                <button type="button" onclick="suggestPassword()" class="mt-2 text-sm text-blue-500 hover:underline">Gerar senha automática</button>
                <p id="suggested-password" class="text-bold mt-1"></p>
            </div>
            <button type="submit" 
                    class="w-full px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                Registrar
            </button>
        </form>
        <p class="text-sm text-center text-gray-600">
            Já tem uma conta? <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">Entrar aqui</a>
        </p>
    </div>
</div>

<script>
    startPhoneField()

    function startPhoneField() {
        document.getElementById('phone').addEventListener('input', function (e) {
            let input = e.target.value;

            // Remove todos os caracteres não numéricos
            input = input.replace(/\D/g, '');

            // Aplica a máscara de telefone celular
            if (input.length > 10) {
                input = input.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
            } else if (input.length > 6) {
                input = input.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
            } else if (input.length > 2) {
                input = input.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
            } else {
                input = input.replace(/^(\d*)/, '($1');
            }

            // Define o valor formatado de volta ao campo
            e.target.value = input;
        });
    }

    function generateStrongPassword(length = 12) {
        const upperCase = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        const lowerCase = "abcdefghijklmnopqrstuvwxyz";
        const numbers = "0123456789";
        const specialChars = "!@#$%^&*()_+[]{}|;:,.<>?";
        const allChars = upperCase + lowerCase + numbers + specialChars;
        
        let password = "";

        password += upperCase[Math.floor(Math.random() * upperCase.length)];
        password += lowerCase[Math.floor(Math.random() * lowerCase.length)];
        password += numbers[Math.floor(Math.random() * numbers.length)];
        password += specialChars[Math.floor(Math.random() * specialChars.length)];

        for (let i = 4; i < length; i++) {
            password += allChars[Math.floor(Math.random() * allChars.length)];
        }

        return password.split('').sort(() => 0.5 - Math.random()).join('');
    }

    function suggestPassword() {
        const suggestedPassword = generateStrongPassword();

        document.getElementById("password").value = suggestedPassword;
        document.getElementById("password_confirmation").value = suggestedPassword;
        document.getElementById("suggested-password").innerText = "Salve sua senha: " + suggestedPassword;
    }
</script>

@endsection