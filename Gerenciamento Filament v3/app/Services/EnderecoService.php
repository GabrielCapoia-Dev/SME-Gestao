<?php

// namespace App\Services;

// use App\Models\Endereco;
// use Filament\Notifications\Notification;

// class EnderecoService
// {


//     public function salvarEndereco(array $data): endereco
//     {
//         if(empty($data)) {
//             Notification::make()
//                 ->title('Erro ao salvar endereço')
//                 ->body('Nenhum endereço informado')
//                 ->error()
//                 ->send();
//             throw new \Exception('Nenhum endereço informado');
//         }
        
//         try {

//             $endereco = new Endereco;

//             $endereco->create([
//                 'cep' => $data['cep'],
//                 'logradouro' => $data['logradouro'],
//                 'bairro' => $data['bairro'],
//                 'cidade' => $data['cidade'],
//                 'uf' => $data['uf'],
//                 'complemento' => $data['complemento'],
//                 'numero' => $data['numero'],
//             ]);

//             return $endereco;

//         } catch (\Exception $e) {

//             Notification::make()
//                 ->title('Erro ao salvar endereço')
//                 ->body($e->getMessage())
//                 ->error()
//                 ->send();
                
//             throw new \Exception($e->getMessage());
//         }
//     }
// }
