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

            // Adicional: escreve VS no centro
            $img->text('VS', 600, 315, function ($font) {
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('middle');
            });

            // Insere Mandante
            if ($mandanteLogoPath && Storage::disk('times_logos')->exists($mandanteLogoPath)) {
                $mandanteImg = Image::read(Storage::disk('times_logos')->path($mandanteLogoPath));
                $mandanteImg->scaleDown(300, 300);
                $img->place($mandanteImg, 'left', 150, 0); // centralizado na vertical à esquerda
            }

            // Insere Visitante
            if ($visitanteLogoPath && Storage::disk('times_logos')->exists($visitanteLogoPath)) {
                $visitanteImg = Image::read(Storage::disk('times_logos')->path($visitanteLogoPath));
                $visitanteImg->scaleDown(300, 300);
                $img->place($visitanteImg, 'right', 150, 0);
            }

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
