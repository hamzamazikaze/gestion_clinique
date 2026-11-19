// JavaScript natif du projet

// 1. Confirmation avant de supprimer
var formulairesSuppression = document.querySelectorAll('.form-suppression');
formulairesSuppression.forEach(function (formulaire) {
    formulaire.addEventListener('submit', function (evenement) {
        var message = formulaire.getAttribute('data-message') || 'Confirmer la suppression ?';
        if (!confirm(message)) {
            evenement.preventDefault();
        }
    });
});

// 2. Ouvrir / fermer le menu sur téléphone
var boutonMenu = document.getElementById('btn-menu');
var sidebar = document.getElementById('sidebar');
if (boutonMenu && sidebar) {
    boutonMenu.addEventListener('click', function () {
        sidebar.classList.toggle('ouverte');
    });
}

// 3. Les messages de succès disparaissent après 5 secondes
var messagesSucces = document.querySelectorAll('.message-succes');
messagesSucces.forEach(function (message) {
    setTimeout(function () {
        message.style.display = 'none';
    }, 5000);
});
