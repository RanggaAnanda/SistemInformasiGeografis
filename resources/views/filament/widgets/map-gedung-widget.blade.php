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

                // Load GeoJSON Kecamatan Bandung
                fetch('/geojson/bandung/kecamatan-bandung.json')
                    .then(res => res.json())
                    .then(data => {
                        L.geoJSON(data, {
                            style: { color: '#3b82f6', weight: 1, fillOpacity: 0.1 }
                        }).addTo(map);
                    });

                // Tambahkan Marker untuk setiap Gedung
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