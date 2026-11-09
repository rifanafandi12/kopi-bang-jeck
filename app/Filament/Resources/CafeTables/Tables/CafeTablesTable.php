<?php

namespace App\Filament\Resources\CafeTables\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

class CafeTablesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nomor')
                    ->label('Nomor Meja')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('aktif')
                    ->label('Aktif')
                    ->boolean(),
                TextColumn::make('token')
                    ->label('Token')
                    ->copyable()
                    ->fontFamily('mono')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                // TOMBOL CETAK QR CODE
                Action::make('cetak_qr')
                    ->label('Cetak QR')
                    ->icon('heroicon-o-qr-code')
                    ->color('success')
                    ->modalHeading(fn ($record) => 'QR Code Meja ' . $record->nomor)
                    ->modalContent(function ($record) {
                        // Buat QR Code menggunakan Endroid
                        $url = route('customer.entry', ['token' => $record->token]);

                        $qrCode = new QrCode(
                            data: $url,
                            encoding: new Encoding('UTF-8'),
                            errorCorrectionLevel: ErrorCorrectionLevel::Low,
                            size: 300,
                            margin: 10,
                            roundBlockSizeMode: RoundBlockSizeMode::Margin,
                        );

                        $writer = new PngWriter();
                        $result = $writer->write($qrCode);
                        $dataUri = $result->getDataUri();

                        return view('filament.qr-modal', [
                            'dataUri' => $dataUri,
                            'nomor' => $record->nomor,
                        ]);
                    })
                    ->modalSubmitAction(false) // Hilangkan tombol submit
                    ->modalCancelActionLabel('Tutup'),

                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
