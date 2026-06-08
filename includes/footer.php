<!-- includes/footer.php -->

<footer class="footer-wave mt-5">
    <div class="container">
        <div class="row g-4">
            <!-- Brand & Environmental Focus -->
            <div class="col-lg-5 col-md-12">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-droplet-half text-info me-2 fs-3"></i>
                    <h4 class="text-white mb-0 font-weight-bold">HYDRO<span class="text-info">CONSULT</span></h4>
                </div>
                <p class="mb-4">
                    La première plateforme digitale intelligente dédiée à la formation, au conseil technique et à la numérisation des services d'ingénierie hydraulique en Algérie.
                </p>
                <div class="eco-banner py-2 px-3 bg-dark-subtle border-start border-info border-3 rounded-2 text-black-50">
                    <h6 class="text-info mb-1"><i class="bi bi-recycle me-2"></i>Engagement Écologique</h6>
                    <small>
                        Nous promouvons l'optimisation des réseaux de distribution pour réduire les fuites d'eau de consommation, la réutilisation sécurisée des eaux usées épurées en agriculture et la sensibilisation pour une gestion rationnelle des ressources hydriques.
                    </small>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-3 col-md-6">
                <h5 class="text-white mb-3">Services & Portails</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="trainings.php" class="footer-link"><i class="bi bi-chevron-right me-1"></i> Formations Pratiques</a></li>
                    <li class="mb-2"><a href="consultations.php" class="footer-link"><i class="bi bi-chevron-right me-1"></i> Consultations Experts</a></li>
                    <li class="mb-2"><a href="studies.php" class="footer-link"><i class="bi bi-chevron-right me-1"></i> Études Techniques</a></li>
                    <li class="mb-2"><a href="community.php" class="footer-link"><i class="bi bi-chevron-right me-1"></i> Espace Communautaire</a></li>
                    <li class="mb-2"><a href="chatbot.php" class="footer-link"><i class="bi bi-chevron-right me-1"></i> HydroBot AI</a></li>
                </ul>
            </div>

            <!-- Contacts & Social -->
            <div class="col-lg-4 col-md-6">
                <h5 class="text-white mb-3">Contactez-nous</h5>
                <p class="mb-2"><i class="bi bi-geo-alt text-info me-2"></i> Alger, Algérie - Bureau d'Innovation Technologique</p>
                <p class="mb-2"><i class="bi bi-envelope text-info me-2"></i> contact@Hydrolicia-dz.com</p>
                <p class="mb-3"><i class="bi bi-phone text-info me-2"></i> +213 (0) 23 45 67 89</p>
                <div class="d-flex gap-2">
                    <a href="#" class="btn btn-sm btn-outline-info rounded-circle"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="btn btn-sm btn-outline-info rounded-circle"><i class="bi bi-linkedin"></i></a>
                    <a href="#" class="btn btn-sm btn-outline-info rounded-circle"><i class="bi bi-youtube"></i></a>
                    <a href="#" class="btn btn-sm btn-outline-info rounded-circle"><i class="bi bi-google-play"></i></a>
                </div>
            </div>
        </div>

        <hr class="my-4 border-secondary">

        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> Hydrolicia Algérie. Tous droits réservés.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <small class="text-white-50">Conçu pour le développement du secteur hydraulique national et la préservation de l'eau.</small>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Global Bootstrap Tooltip Initialization -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>

</body>
</html>
