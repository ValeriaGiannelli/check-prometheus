document.addEventListener('DOMContentLoaded', function () {
    const checkboxes = document.querySelectorAll('.ignore-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            if (this.checked) {
                const logId = this.dataset.logId;
                const description = this.dataset.description;
                const clientId = this.dataset.clientId;

                fetch('/logs/ignore', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        log_id: logId,
                        description: description,
                        client_id: clientId,
                    }),
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(error => {
                            throw new Error(JSON.stringify(error.message));
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        this.closest('tr').remove();
                        alert('Errore marcato come trascurabile.');
                    } else {
                        alert('Errore: ' + JSON.stringify(data.message));
                        this.checked = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Errore durante la richiesta: ' + error.message);
                    this.checked = false;
                });
            }
        });
    });
});