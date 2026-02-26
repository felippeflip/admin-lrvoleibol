<?php

namespace App\Services;

use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class GameImageService
{
    /**
     * Mescla a imagem dos times (Mandante e Visitante) num único banner.
     * Retorna o path relativo do arquivo salvo.
     */
    public function createGameImage($mandanteLogoPath, $visitanteLogoPath, $gameId)
    {
        try {
            // Cria um canvas vazio
            $img = Image::create(1200, 630)->fill('#1e3a8a');

            // Insere Mandante
            if ($mandanteLogoPath && Storage::disk('times_logos')->exists($mandanteLogoPath)) {
                $mandanteImg = Image::read(Storage::disk('times_logos')->path($mandanteLogoPath));
                // scaleDown() reduz proporcionalmente sem esticar, e pad() preenche com transparente 
                // para criar uma caixa exata de 600x600 px centrada
                $mandanteImg->scaleDown(450, 450)->pad(450, 450, 'rgba(0, 0, 0, 0)');
                $img->place($mandanteImg, 'left', 150, 0); // centralizado na vertical à esquerda
            }

            // Insere Visitante
            if ($visitanteLogoPath && Storage::disk('times_logos')->exists($visitanteLogoPath)) {
                $visitanteImg = Image::read(Storage::disk('times_logos')->path($visitanteLogoPath));
                // Mesma padronização para o visitante, garantindo alinhamento independente da orientação (vertical/horizontal)
                $visitanteImg->scaleDown(450, 450)->pad(450, 450, 'rgba(0, 0, 0, 0)');
                $img->place($visitanteImg, 'right', 150, 0);
            }

            // Adicional: escreve X gigantesco no centro absoluto do banner (1200x630 -> 600, 315)
            // Desenhado APÓS as logos para ficar na camada superior
            $img->text('X', 600, 315, function ($font) {
                // Carrega a fonte TrueType para possibilitar o redimensionamento real
                $fontPath = Storage::disk('public')->path('font.ttf');
                if (file_exists($fontPath)) {
                    $font->file($fontPath);
                }
                $font->color('#ffffff');
                // Alinha o texto precisamente no centro das coordenadas passadas
                $font->align('center');
                $font->valign('middle');
                // Aumenta o tamanho da fonte
                $font->size(200); 
            });

            // Garante que a pasta existe
            if (!Storage::disk('public')->exists('jogos_imagens')) {
                Storage::disk('public')->makeDirectory('jogos_imagens');
            }

            $fileName = "jogo_{$gameId}_" . time() . ".jpg";
            $savePath = Storage::disk('public')->path('jogos_imagens/' . $fileName);

            // Salva como JPG
            $img->toJpeg(90)->save($savePath);

            return 'jogos_imagens/' . $fileName;

        } catch (\Exception $e) {
            Log::error("Erro em GameImageService ao mesclar imagens: " . $e->getMessage());
            return null;
        }
    }
}
