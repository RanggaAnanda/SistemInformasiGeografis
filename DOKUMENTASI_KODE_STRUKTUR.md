# Dokumentasi Lengkap - Sistem Informasi Geografis Bandung

Dokumentasi ini mengorganisir semua kode berdasarkan fitur/modul, di mana setiap section berisi: **Model → Resource → Migration → View (jika ada)**.

---

## 📋 Daftar Isi

1. [Kategori Asset](#1-kategori-asset)
2. [Asset Bergerak](#2-asset-bergerak)
3. [Gedung](#3-gedung)
4. [District (Kecamatan)](#4-district-kecamatan)
5. [Mutasi Asset](#5-mutasi-asset)
6. [User](#6-user)
7. [Filament Setup](#7-filament-setup)

---

## 1. Kategori Asset

### Model: `app/Models/KategoriAsset.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriAsset extends Model
{
    protected $table = 'kategori_asset';

    protected $fillable = [
        'nama_kategori',
        'fields'
    ];
    
    protected $casts = [
        'fields' => 'array', 
    ];

    public function assets(): HasMany
    {
        return $this->hasMany(AssetBergerak::class, 'asset_category_id');
    }
}
```

### Resource: `app/Filament/Admin/Resources/KategoriAssetResource.php`

```php
<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\KategoriAssetResource\Pages;
use App\Filament\Admin\Resources\KategoriAssetResource\RelationManagers;
use App\Models\KategoriAsset;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KategoriAssetResource extends Resource
{
    protected static ?string $model = KategoriAsset::class;
    protected static ?string $navigationLabel = 'Kategori Asset';
    protected static ?string $navigationGroup = 'Data Aset';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\TextInput::make('nama_kategori')
                        ->required()
                        ->unique(ignoreRecord: true),

                    Forms\Components\Repeater::make('fields')
                        ->label('Daftar Atribut Tambahan')
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('Nama Atribut')
                                ->required(),
                        ])
                        ->columns(2)
                        ->createItemButtonLabel('Tambah Atribut Baru')
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_kategori')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('fields')
                    ->label('Atribut')
                    ->getStateUsing(fn ($record) => collect($record->fields)->pluck('name')->implode(', '))
                    ->separator(',') 
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKategoriAssets::route('/'),
            'create' => Pages\CreateKategoriAsset::route('/create'),
            'edit' => Pages\EditKategoriAsset::route('/{record}/edit'),
        ];
    }
}
```

### Migration: `database/migrations/2026_01_11_132714_create_kategori_assets_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_asset', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kategori');
            $table->json('fields')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_asset');
    }
};
```

---

## 2. Asset Bergerak

### Model: `app/Models/AssetBergerak.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetBergerak extends Model
{
    protected $table = 'asset_bergerak';

    protected $fillable = [
        'kode_aset',
        'nama_aset',
        'jenis',
        'gedung_id',
        'status',
        'kategori_asset_id',
        'value'
    ];

    protected $casts = ['value' => 'array'];

    public function gedung()
    {
        return $this->belongsTo(Gedung::class);
    }

    public function mutations()
    {
        return $this->hasMany(MutasiAsset::class);
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriAsset::class, 'kategori_asset_id');
    }
}
```

### Resource: `app/Filament/Admin/Resources/AssetBergerakResource.php`

```php
<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AssetBergerakResource\Pages;
use App\Models\AssetBergerak;
use App\Models\KategoriAsset;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Filters\SelectFilter;

class AssetBergerakResource extends Resource
{
    protected static ?string $model = AssetBergerak::class;
    protected static ?string $navigationLabel = 'Asset Bergerak';
    protected static ?string $navigationGroup = 'Data Aset';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Utama')
                    ->schema([
                        TextInput::make('kode_aset')
                            ->required()
                            ->unique(ignoreRecord: true),
                        TextInput::make('nama_aset')->required(),
                        TextInput::make('jenis')->required(),
                    ]),

                Section::make('Lokasi & Kategori')
                    ->schema([
                        Select::make('gedung_id')
                            ->relationship('gedung', 'nama_gedung')
                            ->required(),
                        Select::make('kategori_asset_id')
                            ->relationship('kategori', 'nama_kategori')
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn(Get $get) => $get),
                    ]),

                Section::make('Atribut Berdasarkan Kategori')
                    ->schema([
                        // Dynamic fields berdasarkan kategori dipilih
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_aset')->searchable()->sortable(),
                TextColumn::make('jenis')->searchable(),
                TextColumn::make('kategori.nama_kategori')->label('Kategori'),
                TextColumn::make('gedung.nama_gedung')->label('Gedung'),
                TextColumn::make('value')->label('Nilai'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'primary' => 'aktif',
                        'warning' => 'dipindahkan',
                        'danger' => 'rusak',
                    ]),
            ])
            ->filters([
                SelectFilter::make('kategori_asset_id')
                    ->relationship('kategori', 'nama_kategori'),
                SelectFilter::make('jenis'),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssetBergeraks::route('/'),
            'create' => Pages\CreateAssetBergerak::route('/create'),
            'edit' => Pages\EditAssetBergerak::route('/{record}/edit'),
        ];
    }
}
```

### Migration: `database/migrations/2026_01_11_134433_create_asset_bergerak_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_bergerak', function (Blueprint $table) {
            $table->id();
            $table->string('kode_aset')->unique();
            $table->string('nama_aset');
            $table->string('jenis');
            $table->foreignId('gedung_id')->constrained('gedung')->cascadeOnDelete();
            $table->foreignId('kategori_asset_id')->constrained('kategori_asset')->cascadeOnDelete();
            $table->enum('status', ['aktif', 'dipindahkan', 'rusak'])->default('aktif');
            $table->json('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_bergerak');
    }
};
```

---

## 3. Gedung

### Model: `app/Models/Gedung.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gedung extends Model
{
    protected $table = 'gedung';
    
    protected $fillable = [
        'district_id',
        'nama_gedung',
        'jenis',
        'alamat',
        'latitude',
        'longitude'
    ];

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function movableAssets()
    {
        return $this->hasMany(AssetBergerak::class);
    }
}
```

### Resource: `app/Filament/Admin/Resources/GedungResource.php`

```php
<?php

namespace App\Filament\Admin\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Gedung;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use App\Filament\Admin\Resources\GedungResource\Pages;

class GedungResource extends Resource
{
    protected static ?string $model = Gedung::class;
    protected static ?string $navigationLabel = 'Gedung';
    protected static ?string $navigationGroup = 'Data Aset';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Lokasi Gedung')
                ->schema([
                    Placeholder::make('map')
                        ->content(fn($get) => view('filament.components.leaflet-map', [
                            'latitude' => $get('latitude') ?? -6.9175,
                            'longitude' => $get('longitude') ?? 107.6191,
                        ])),

                    TextInput::make('latitude')
                        ->required()
                        ->numeric()
                        ->reactive(),

                    TextInput::make('longitude')
                        ->required()
                        ->numeric()
                        ->reactive(),
                ]),

            Section::make('Informasi Gedung')
                ->schema([
                    Forms\Components\Select::make('district_id')
                        ->label('Kecamatan')
                        ->relationship('district', 'nama_kecamatan')
                        ->searchable()
                        ->preload()
                        ->required(),

                    TextInput::make('nama_gedung')->required(),
                    TextInput::make('jenis')->required(),

                    Forms\Components\Textarea::make('alamat')
                        ->nullable()
                        ->reactive(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_gedung')->searchable(),
                TextColumn::make('jenis'),
                TextColumn::make('district.nama_kecamatan')->label('Kecamatan'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGedungs::route('/'),
            'create' => Pages\CreateGedung::route('/create'),
            'edit' => Pages\EditGedung::route('/{record}/edit'),
        ];
    }
}
```

### Migration: `database/migrations/2026_01_11_134335_create_gedungs_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gedung', function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_id')->constrained('districts')->cascadeOnDelete();
            $table->string('nama_gedung');
            $table->string('jenis');
            $table->text('alamat')->nullable();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gedung');
    }
};
```

### View: `resources/views/filament/components/leaflet-map.blade.php`

```blade
<div x-data="leafletMap()" x-init="initMap()" class="w-full" wire:ignore>
    <div x-ref="map" class="w-full rounded-lg border shadow-sm" style="height: 450px;"></div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

    <script>
        function leafletMap() {
            return {
                map: null,
                marker: null,
                districts: @json(\App\Models\District::pluck('id', 'nama_kecamatan')),
                geojsonLayer: null,

                initMap() {
                    const lat = @js($latitude ?? -6.9175);
                    const lng = @js($longitude ?? 107.6191);

                    this.map = L.map(this.$refs.map).setView([lat, lng], 13);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(this.map);

                    fetch('/geojson/bandung/kecamatan-bandung.json')
                        .then(res => res.json())
                        .then(data => {
                            L.geoJSON(data, {
                                style: { color: '#3b82f6', weight: 2, fillOpacity: 0.1 }
                            }).addTo(this.map);
                        });

                    if (lat && lng) {
                        this.setMarker(lat, lng, true);
                    }

                    if (typeof L.Control.Geocoder !== 'undefined') {
                        L.Control.geocoder().addTo(this.map);
                    }

                    this.map.on('click', e => {
                        this.setMarker(e.latlng.lat, e.latlng.lng, true);
                    });
                },

                async setMarker(lat, lng, updateAddress = false, address = null) {
                    if (this.marker) {
                        this.marker.setLatLng([lat, lng]);
                    } else {
                        this.marker = L.marker([lat, lng]).addTo(this.map);
                    }

                    @this.set('data.latitude', lat);
                    @this.set('data.longitude', lng);

                    if (updateAddress) {
                        await this.reverseGeocode(lat, lng);
                    }
                },

                async reverseGeocode(lat, lng) {
                    try {
                        const response = await fetch(
                            `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`
                        );
                        const data = await response.json();
                        if (data.address) {
                            @this.set('data.alamat', data.display_name);
                        }
                    } catch (e) {
                        console.error('Reverse geocode error:', e);
                    }
                }
            }
        }
    </script>
</div>
```

---

## 4. District (Kecamatan)

### Model: `app/Models/District.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $fillable = [
        'kode_kecamatan',
        'nama_kecamatan',
        'nama_wilayah',
    ];
}
```

### Migration: `database/migrations/2026_01_11_120952_create_districts_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kecamatan')->unique();
            $table->string('nama_kecamatan');
            $table->string('nama_wilayah')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('districts');
    }
};
```

### Console Command: `app/Console/Commands/ImportBandungDistricts.php`

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Models\District;

class ImportBandungDistricts extends Command
{
    protected $signature = 'import:bandung-districts';

    protected $description = 'Import data kecamatan Bandung dari GeoJSON';

    public function handle()
    {
        $path = public_path('geojson/bandung/kecamatan-bandung.json');

        if (!File::exists($path)) {
            $this->error('File GeoJSON tidak ditemukan.');
            return;
        }

        $geojson = json_decode(File::get($path), true);

        foreach ($geojson['features'] as $feature) {
            District::updateOrCreate(
                ['kode_kecamatan' => $feature['properties']['id_kecamatan']],
                [
                    'nama_kecamatan' => $feature['properties']['nama_kecamatan'],
                    'nama_wilayah'   => $feature['properties']['nama_wilayah'],
                ]
            );
        }

        $this->info('Import kecamatan Bandung selesai.');
    }
}
```

---

## 5. Mutasi Asset

### Model: `app/Models/MutasiAsset.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MutasiAsset extends Model
{
    protected $table = 'mutasi_asset';

    protected $fillable = [
        'asset_bergerak_id',
        'from_gedung_id',
        'to_gedung_id',
        'requested_by',
        'approved_by',
        'status',
        'tanggal_pengajuan',
        'tanggal_disetujui'
    ];

    public function assetBergerak()
    {
        return $this->belongsTo(AssetBergerak::class);
    }

    public function fromGedung()
    {
        return $this->belongsTo(Gedung::class, 'from_gedung_id');
    }

    public function toGedung()
    {
        return $this->belongsTo(Gedung::class, 'to_gedung_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
```

### Migration: `database/migrations/2026_01_11_134530_create_mutasi_asset_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mutasi_asset', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_bergerak_id')->constrained('asset_bergerak')->cascadeOnDelete();
            $table->foreignId('from_gedung_id')->constrained('gedung');
            $table->foreignId('to_gedung_id')->constrained('gedung');
            $table->foreignId('requested_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->date('tanggal_pengajuan');
            $table->date('tanggal_disetujui')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mutasi_asset');
    }
};
```

---

## 6. User

### Model: `app/Models/User.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
```

### Migration: `database/migrations/0001_01_01_000000_create_users_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
```

---

## 7. Filament Setup

### AdminPanelProvider: `app/Providers/Filament/AdminPanelProvider.php`

```php
<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
            ->widgets([
                \App\Filament\Widgets\MapGedungWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
```

### Widget: `app/Filament/Admin/Widgets/MapGedungWidget.php`

```php
<?php

namespace App\Filament\Widgets;

use App\Models\Gedung;
use Filament\Widgets\Widget;

class MapGedungWidget extends Widget
{
    protected static string $view = 'filament.widgets.map-gedung-widget';
    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        return [
            'locations' => Gedung::with('district')->get()->map(function ($gedung) {
                return [
                    'lat' => (float) $gedung->latitude,
                    'lng' => (float) $gedung->longitude,
                    'nama' => $gedung->nama_gedung,
                    'jenis' => $gedung->jenis,
                    'kecamatan' => $gedung->district?->nama_kecamatan ?? '-',
                ];
            })->toArray(),
        ];
    }
}
```

### Widget View: `resources/views/filament/widgets/map-gedung-widget.blade.php`

```blade
<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">Peta Sebaran Asset Kota Bandung</x-slot>
        
        <div x-data="{
            locations: {{ json_encode($locations) }},
            init() {
                const map = L.map($refs.mapDashboard).setView([-6.9175, 107.6191], 13);
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap'
                }).addTo(map);

                fetch('/geojson/bandung/kecamatan-bandung.json')
                    .then(res => res.json())
                    .then(data => {
                        L.geoJSON(data, {
                            style: { color: '#3b82f6', weight: 1, fillOpacity: 0.1 }
                        }).addTo(map);
                    });

                this.locations.forEach(loc => {
                    if (loc.lat && loc.lng) {
                        L.marker([loc.lat, loc.lng])
                            .addTo(map)
                            .bindPopup(`
                                <strong>${loc.nama}</strong><br>
                                Jenis: ${loc.jenis}<br>
                                Kecamatan: ${loc.kecamatan}
                            `);
                    }
                });
            }
        }" class="w-full">
            <div x-ref="mapDashboard" class="w-full rounded-lg border shadow-inner" style="height: 500px; z-index: 1;"></div>
            
            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
```

---

**Dokumentasi Lengkap Selesai!** 

Setiap section di atas sudah terorganisir dengan rapi:
- **Model** - Struktur data
- **Resource** - Form & Tabel di Filament
- **Migration** - Schema database
- **View** - Template Blade (jika ada)

Anda bisa menyalin kode dari masing-masing bagian sesuai kebutuhan.
