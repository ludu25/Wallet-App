@extends('layouts.app')

@section('content')
<div class="flex min-h-screen p-8 bg-gradient-to-r from-blue-500 to-purple-600">
    <div class="w-full p-8 space-y-6 bg-white rounded-lg shadow-lg">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold mb-4">Dashboard</h1>

            <div class="flex space-x-2">
                <!-- Botão de converter a moeda -->
                <button type="submit" class="flex items-center mr-4 bg-green-500 text-white font-bold py-2 px-4 rounded-full hover:bg-green-700"
                        onclick="convertBalanceToUSD()">
                    <img src="https://upload.wikimedia.org/wikipedia/en/a/a4/Flag_of_the_United_States.svg" 
                        alt="Bandeira dos EUA"
                        class="w-8 h-8 mr-2"> USD
                </button>

                <!-- Botão de Logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="bg-red-500 text-white font-bold py-2 px-4 rounded-full hover:bg-red-700">
                        Sair
                    </button>
                </form>
            </div>
        </div>
        
        <div class="flex flex-col md:flex-row">
            {{-- Card com informações do usuário --}}
            <div class="bg-gray-100 p-4 rounded-lg mb-4 md:mr-4 md:mb-0 flex-1">
                <h2 class="text-xl font-semibold">Minhas Informações</h2>
                <p class="py-3"><strong>Nome:</strong> {{ Auth::user()->name }}</p>
                <p class="py-3"><strong>Email:</strong> {{ Auth::user()->email }}</p>
                <p class="py-3">
                    <strong>Telefone:</strong> 
                    {{ Auth::user()->phone }}
                    <button type="button" 
                            class="p-4 bg-green-500 text-white font-bold py-2 rounded hover:bg-green-700"
                            onclick="openValidatePhoneModal()">
                        Validar Número
                    </button>
                </p>
                <p class="py-3">
                    <strong>Saldo:</strong> 
                    <span id="balance_text"> R$ {{ Auth::user()->wallet->balance ?? 0 }} </span>
                    <button type="button" 
                            class="p-4 bg-green-500 text-white font-bold py-2 rounded hover:bg-green-700"
                            onclick="openRechargeModal()">
                        Recarregar
                    </button>
                </p>
            </div>

            {{-- Card para fazer uma nova transacao --}}
            <div class="bg-gray-100 p-4 rounded-lg flex-1">
                <h2 class="text-xl font-semibold">Faca uma transacão para alguém:</h2>

                @if (session('transfer_success'))
                    <div class="bg-green-500 text-white p-4 rounded mt-2">
                        {{ session('transfer_success') }}
                    </div>
                @endif

                @if (session('transfer_error'))
                    <div class="bg-red-500 text-white p-4 rounded mt-2">
                        {{ session('transfer_error') }}
                    </div>
                @endif

                @if (!session('transfer_error') && (Auth::user()->wallet ? Auth::user()->wallet->balance == 0 : 1))
                    <div class="bg-red-500 text-white p-4 rounded mt-2">
                        Saldo insuficiente, recarregue sua carteira!
                    </div>
                @endif

                <form action="{{ route('transaction.send') }}" method="POST">
                    @csrf
                    <div class="mt-4">
                        <label for="recipient_email" class="block text-sm font-medium text-gray-600">E-mail do destinatário:</label>
                        <input type="email" name="recipient_email" id="recipient_email" required
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring focus:ring-blue-300" 
                            placeholder="Digite o e-mail do usuário" />
                    </div>
                    
                    <div class="mt-4">
                        <label for="amount" class="block text-sm font-medium text-gray-600">Valor a ser enviado:</label>
                        <input type="number" name="amount" id="amount" required min="1"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring focus:ring-blue-300" 
                            placeholder="Digite o valor" />
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="p-4 bg-green-500 text-white font-bold py-2 rounded hover:bg-green-700">
                            Enviar Dinheiro
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        {{-- Listagem de transacões --}}
        <div class="mt-6">
            <h2 class="text-xl font-semibold">Detalhes</h2>
            <div class="flex justify-between items-center space-x-4 mt-2">
                <button class="bg-gray-200 py-2 px-4 rounded hover:bg-gray-300">Ver Histórico de Transações</button>

                <button onclick="generateChart()" class="bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-700">
                    📊 Mostrar Gráfico de Gastos
                </button>
            </div>

            <table class="w-full bg-white border border-gray-200">
                <thead>
                    <tr>
                        <th class="text-center py-2 px-4 border-b">Remetente</th>
                        <th class="text-center py-2 px-4 border-b">Destinatário</th>
                        <th class="text-center py-2 px-4 border-b">Valor</th>
                        <th class="text-center py-2 px-4 border-b">Data</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($transactions->isEmpty())
                        <tr>
                            <td colspan="4" class="py-2 px-4 text-center text-gray-500">Nenhuma transação encontrada.</td>
                        </tr>
                    @else
                        @foreach($transactions as $transaction)
                            <tr>
                                <td class="text-center py-2 px-4 border-b
                                        {{ $transaction->sender_id === Auth::id() ? 'bg-yellow-200' : '' }}"
                                    >
                                    {{ $transaction->sender_id === Auth::id() ? '👤 Você (Remetente)' : $transaction->sender->name . ' (' . $transaction->sender->email . ')' }}
                                </td>
                                <td class="text-center py-2 px-4 border-b
                                        {{ $transaction->receiver_id === Auth::id() ? 'bg-yellow-200' : '' }}"
                                    >
                                    {{ $transaction->receiver_id === Auth::id() ? '👤 Você (Destinatário)' : $transaction->receiver->name . ' (' . $transaction->receiver->email . ')' }}
                                </td>
                                <td class="text-center py-2 px-4 border-b text-white
                                        {{ $transaction->receiver->id === Auth::user()->id ? 'bg-green-700' : 'bg-red-700' }}"
                                    >
                                    @if ($transaction->receiver->id === Auth::user()->id)
                                        ➕
                                    @else
                                        ➖
                                    @endif

                                    R$ {{ $transaction->amount }}
                                </td>
                                <td class="text-center py-2 px-4 border-b">{{ $transaction->created_at->subHours(3)->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>

            <!-- Canvas para o gráfico -->
            <canvas id="myChart" class="hidden mt-4"></canvas>
        </div>
    </div>
</div>

<div id="validatePhoneModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-sm">
        <h2 class="text-xl font-semibold mb-4">Você deseja validar o seu número de celular?</h2>

        <h4 class="text-green-600 text-x2 font-semibold mb-4" id="validationResultSuccess"><h4>
        <h4 class="text-red-600 text-x2 font-semibold mb-4" id="validationResultFail"><h4>

        <!-- Buttons -->
        <div class="flex justify-end">
            <button 
                type="button" 
                class="bg-gray-500 text-white px-4 py-2 rounded mr-2"
                onclick="closeValidatePhoneModal()"
            >
                Cancelar
            </button>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                    onclick="validatePhoneNumber()">
                Sim
            </button>
        </div>
    </div>
</div>

<div id="reloadWalletModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-sm">
        <h2 class="text-xl font-semibold mb-4">Recarregar Carteira</h2>

        <form method="POST" action="{{ route('wallet.update') }}">
            @csrf

            <!-- Amount Input -->
            <label for="amount" class="block text-sm font-medium text-gray-700">Valor</label>
            <input 
                type="number" 
                name="amount" 
                id="amount" 
                min="1" 
                class="w-full p-2 border border-gray-300 rounded mt-1 mb-4"
                required 
            >

            <!-- Buttons -->
            <div class="flex justify-end">
                <button 
                    type="button" 
                    class="bg-gray-500 text-white px-4 py-2 rounded mr-2"
                    onclick="closeRechargeModal()"
                >
                    Cancelar
                </button>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Recarregar
                </button>
            </div>
        </form>
    </div>
</div>

<script>

    function generateChart() {
        const labels = ['Entradas', 'Saídas'];
        const amounts = [
            {{ 
                $transactions->where('receiver_id', '=', Auth::user()->id)
                ->where('amount', '>', 0)
                ->sum('amount') 
            }}, // Soma das entradas
            {{ 
                $transactions->where('sender_id', '=', Auth::user()->id)
                ->where('amount', '>', 0)
                ->sum('amount') 
            }}  // Soma das saídas (como valor negativo)
        ];

        const ctx = document.getElementById('myChart').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'bar', // Você pode mudar para 'pie', 'line', etc.
            data: {
                labels: labels,
                datasets: [{
                    label: 'Transações',
                    data: amounts,
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.2)', // Cor para entradas
                        'rgba(255, 99, 132, 0.2)' // Cor para saídas
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 99, 132, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Exibir o gráfico e ocultar o botão após a geração
        document.getElementById('myChart').classList.remove('hidden');
    }

    function convertBalanceToUSD() {
        let userBalance = {{ Auth::user()->wallet->balance ?? 0 }};

        fetch('/convert_to_usd', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: []
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro na resposta da API');
            }
            return response.json();
        })
        .then(data => {
            let string = '';

            if (data.result == 'success') {
                let usdBalance = data.conversion_rates['USD'] * userBalance;
                string += `$ ${usdBalance}`;
            } else {
                string = 'R$ ' + balance_text;
            }

            document.getElementById('balance_text').innerText = string;
        })
        .catch(error => {
            console.error('Erro:', error);

            document.getElementById('balance_text').innerText = 'R$ ' + userBalance;
        });
    }

    function validatePhoneNumber() {
        fetch('/validate_phone', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: []
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro na resposta da API');
            }
            return response.json();
        })
        .then(data => {
            let string = '';

            if (data.valid) {
                string += `O número de celular ${data.number} (${data.location}) é válido!`;
            } else {
                string = 'O número de celular não é válido!';
            }

            document.getElementById('validationResultSuccess').innerText = string;
        })
        .catch(error => {
            console.error('Erro:', error);
            document.getElementById('validationResultFail').innerText = 'Houve um erro ao validar número, tente novamente!';
        });
    }

    function openValidatePhoneModal() {
        document.getElementById('validatePhoneModal').classList.remove('hidden');
    }

    function closeValidatePhoneModal() {
        document.getElementById('validationResultSuccess').innerText = '';
        document.getElementById('validationResultFail').innerText = '';

        document.getElementById('validatePhoneModal').classList.add('hidden');
    }

    function openRechargeModal() {
        document.getElementById('reloadWalletModal').classList.remove('hidden');
    }

    function closeRechargeModal() {
        document.getElementById('reloadWalletModal').classList.add('hidden');
    }
</script>
@endsection