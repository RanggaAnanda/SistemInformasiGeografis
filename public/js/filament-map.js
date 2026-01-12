(function waitForMap() {
    if (window.leafletMap && window.leafletMap._loaded) {

        console.log('Leaflet map ready');

        // =============================
        // SEARCH CONTROL
        // =============================
        const geocoder = L.Control.geocoder({
            placeholder: 'Cari lokasi...',
            defaultMarkGeocode: false
        })
        .on('markgeocode', function (e) {
            const latlng = e.geocode.center;

            window.leafletMap.setView(latlng, 16);

            Livewire.dispatch('setLocation', {
                lat: latlng.lat,
                lng: latlng.lng
            });
        });

        geocoder.addTo(window.leafletMap);

        return;
    }

    // retry tiap 300ms sampai map siap
    setTimeout(waitForMap, 300);
})();
