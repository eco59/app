function filterEvents() {
    const dateFilter = document.getElementById('dateFilter').value;
    const dateEndFilter = document.getElementById('dateEndFilter').value;
    const playerFilter = document.getElementById('playerFilter').value;
    const pseudoFilter = document.getElementById('pseudoFilter').value.toLowerCase(); // Obtenir le pseudo à filtrer

    const eventItems = document.querySelectorAll('.event-item');

    eventItems.forEach(event => {
        const eventDate = event.getAttribute('data-start-date');
        const eventEndDate = event.getAttribute('data-end-date');
        const eventPlayers = event.getAttribute('data-player-count');
        const eventPseudo = event.getAttribute('data-pseudo'); // Récupérer le pseudo de l'attribut data-pseudo

        // Conditions de filtrage par date
        const dateCondition = !dateFilter || eventDate >= dateFilter;
        const dateEndCondition = !dateEndFilter || eventEndDate <= dateEndFilter;

        // Condition de filtrage par nombre de joueurs
        const playerCondition = !playerFilter || parseInt(eventPlayers) >= parseInt(playerFilter);

        // Condition de filtrage par pseudo (les trois premières lettres)
        const pseudoCondition = pseudoFilter.length < 3 || eventPseudo.startsWith(pseudoFilter); // Vérifier si le pseudo commence par le texte

        // Afficher ou cacher l'événement selon les conditions
        if (dateCondition && dateEndCondition && playerCondition && pseudoCondition) {
            event.style.display = 'block';
        } else {
            event.style.display = 'none';
        }
    });
}