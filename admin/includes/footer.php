        </div> <!-- End admin-content-body -->
    </div> <!-- End admin-main -->

    <!-- Bootstrap 5 Bundle with Popper JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Sidebar Toggle Script & Tooltip Init -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            
            if (toggle && sidebar) {
                toggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    sidebar.classList.toggle('show');
                });
                
                // Close sidebar when clicking outside on mobile
                document.addEventListener('click', function(e) {
                    if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
                        sidebar.classList.remove('show');
                    }
                });
            }

            // Global Bootstrap Tooltip Initialization
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
</body>
</html>
