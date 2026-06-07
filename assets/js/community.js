// assets/js/community.js

document.addEventListener('DOMContentLoaded', function() {
    // 1. Category Filter Logic
    const categoryLinks = document.querySelectorAll('.category-filter');
    const questionCards = document.querySelectorAll('.question-card');
    
    categoryLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all filters
            categoryLinks.forEach(l => l.classList.remove('active', 'bg-primary', 'text-white'));
            categoryLinks.forEach(l => l.classList.add('bg-light', 'text-dark'));
            
            // Add active to current
            this.classList.remove('bg-light', 'text-dark');
            this.classList.add('active', 'bg-primary', 'text-white');
            
            const selectedCategory = this.getAttribute('data-category');
            
            questionCards.forEach(card => {
                if (selectedCategory === 'all' || card.getAttribute('data-category') === selectedCategory) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    // 2. Submit Question AJAX
    const askForm = document.getElementById('askForm');
    if (askForm) {
        askForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btnAskSubmit = document.getElementById('btnAskSubmit');
            const askAlert = document.getElementById('askAlert');
            
            btnAskSubmit.disabled = true;
            btnAskSubmit.innerHTML = 'Publication... <span class="spinner-border spinner-border-sm" role="status"></span>';
            
            const formData = new FormData(this);
            formData.append('action', 'ask');
            
            fetch('api/forum.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                btnAskSubmit.disabled = false;
                btnAskSubmit.innerHTML = 'Poser ma question <i class="bi bi-send-fill ms-1"></i>';
                
                askAlert.classList.remove('d-none', 'alert-success', 'alert-danger');
                if (data.success) {
                    askAlert.classList.add('alert-success');
                    askAlert.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i> Question publiée avec succès ! Rechargement...';
                    
                    // Reset form
                    askForm.reset();
                    
                    // Reload page after 1.5 seconds to show question in list
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    askAlert.classList.add('alert-danger');
                    askAlert.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i>' + data.message;
                }
            })
            .catch(err => {
                btnAskSubmit.disabled = false;
                btnAskSubmit.innerHTML = 'Poser ma question <i class="bi bi-send-fill ms-1"></i>';
                askAlert.classList.remove('d-none');
                askAlert.classList.add('alert-danger');
                askAlert.innerHTML = 'Erreur réseau.';
            });
        });
    }

    // 3. Load Replies for Accordion/Question Cards
    // Add event listener to elements with details trigger
    const collapseElements = document.querySelectorAll('.question-collapse');
    collapseElements.forEach(collapseEl => {
        collapseEl.addEventListener('show.bs.collapse', function() {
            const questionId = this.getAttribute('data-question-id');
            loadReplies(questionId);
        });
    });
});

// Function to load replies from the database for a specific question
function loadReplies(questionId) {
    const repliesContainer = document.getElementById(`replies-container-${questionId}`);
    if (!repliesContainer) return;
    
    // Show spinner
    repliesContainer.innerHTML = `
        <div class="text-center py-3">
            <div class="spinner-border text-primary spinner-border-sm" role="status"></div>
            <small class="text-muted ms-2 d-block">Chargement des réponses...</small>
        </div>
    `;
    
    fetch(`api/forum.php?action=get_replies&question_id=${questionId}`)
    .then(response => response.json())
    .then(data => {
        repliesContainer.innerHTML = '';
        
        if (data.success) {
            if (data.replies.length === 0) {
                repliesContainer.innerHTML = `
                    <p class="text-muted small text-center my-3"><i class="bi bi-chat-left-dots me-2"></i>Aucune réponse pour l'instant. Soyez le premier à répondre !</p>
                `;
            } else {
                data.replies.forEach(reply => {
                    const badgeClass = getRoleBadgeClass(reply.role);
                    
                    const replyHtml = `
                        <div class="reply-box mb-3 border-start border-4 border-info">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <strong class="small text-dark">${escapeHtml(reply.author)}</strong>
                                    <span class="badge badge-role ${badgeClass} ms-2">${escapeHtml(reply.role)}</span>
                                </div>
                                <small class="text-muted small"><i class="bi bi-clock me-1"></i>${formatDate(reply.created_at)}</small>
                            </div>
                            <p class="text-secondary small mb-0" style="white-space: pre-line;">${escapeHtml(reply.content)}</p>
                        </div>
                    `;
                    repliesContainer.insertAdjacentHTML('beforeend', replyHtml);
                });
            }
        } else {
            repliesContainer.innerHTML = `<p class="text-danger small">Erreur: ${escapeHtml(data.message)}</p>`;
        }
    })
    .catch(err => {
        repliesContainer.innerHTML = '<p class="text-danger small">Erreur de connexion réseau.</p>';
    });
}

// Function to post a reply
function submitReply(event, questionId) {
    event.preventDefault();
    
    const form = event.target;
    const btnSubmit = form.querySelector('button[type="submit"]');
    const contentTextarea = form.querySelector('textarea[name="content"]');
    const authorInput = form.querySelector('input[name="author"]');
    const roleSelect = form.querySelector('select[name="role"]');
    
    if (!contentTextarea.value.trim() || !authorInput.value.trim() || !roleSelect.value) {
        alert('Veuillez remplir tous les champs obligatoires.');
        return;
    }
    
    btnSubmit.disabled = true;
    btnSubmit.innerHTML = 'Envoi... <span class="spinner-border spinner-border-sm" role="status"></span>';
    
    const formData = new FormData(form);
    formData.append('action', 'reply');
    formData.append('question_id', questionId);
    
    fetch('api/forum.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = 'Répondre <i class="bi bi-reply-fill ms-1"></i>';
        
        if (data.success) {
            // Reload replies
            loadReplies(questionId);
            
            // Clear content textarea only (keep name & role for convenience)
            contentTextarea.value = '';
            
            // Update reply counter badge on UI
            const counterBadge = document.getElementById(`reply-count-${questionId}`);
            if (counterBadge) {
                let currentCount = parseInt(counterBadge.innerText);
                counterBadge.innerText = (currentCount + 1) + " réponses";
            }
        } else {
            alert('Erreur: ' + data.message);
        }
    })
    .catch(err => {
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = 'Répondre <i class="bi bi-reply-fill ms-1"></i>';
        alert('Erreur réseau lors de l\'envoi.');
    });
}

// Helpers
function getRoleBadgeClass(role) {
    if (role === 'Étudiant' || role === 'Etudiant') return 'badge-student';
    if (role === 'Professionnel' || role === 'Professionel') return 'badge-professional';
    return 'badge-expert';
}

function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString.replace(/-/g, "/"));
    return date.toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function escapeHtml(str) {
    return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}
