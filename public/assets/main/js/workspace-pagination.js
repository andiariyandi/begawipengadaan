/**
 * BegawiPBJ Workspace Modular Pagination Engine
 * 
 * Automatically enhances containers with `.ws-paginated-container` class.
 * Works seamlessly across multi-section dashboards without reloading.
 */
(function() {
    'use strict';

    function initWsPagination() {
        var containers = document.querySelectorAll('.ws-paginated-container');
        if (!containers || containers.length === 0) return;

        containers.forEach(function(container) {
            // Avoid double init
            if (container.dataset.wsPaginationInitialized === 'true') {
                return;
            }

            var pageSize = parseInt(container.getAttribute('data-page-size') || '4', 10);
            if (isNaN(pageSize) || pageSize < 1) pageSize = 4;

            var items = container.querySelectorAll('.ws-paginated-item');
            var totalItems = items.length;

            var existingControls = container.querySelector('.ws-pagination-controls');
            if (existingControls) {
                existingControls.remove();
            }

            if (totalItems <= pageSize) {
                // Show all and exit
                items.forEach(function(item) {
                    item.style.display = '';
                });
                return;
            }

            var totalPages = Math.ceil(totalItems / pageSize);
            var currentPage = 1;

            // Create controls container
            var controls = document.createElement('div');
            controls.className = 'ws-pagination-controls';

            var infoSpan = document.createElement('span');
            infoSpan.className = 'ws-page-info';

            var nav = document.createElement('div');
            nav.className = 'ws-page-nav';

            // Prev Button
            var prevBtn = document.createElement('button');
            prevBtn.type = 'button';
            prevBtn.className = 'ws-page-btn ws-page-btn-nav';
            prevBtn.innerHTML = '<i class="fas fa-chevron-left me-1"></i> Prev';

            // Next Button
            var nextBtn = document.createElement('button');
            nextBtn.type = 'button';
            nextBtn.className = 'ws-page-btn ws-page-btn-nav';
            nextBtn.innerHTML = 'Next <i class="fas fa-chevron-right ms-1"></i>';

            // Page numbers container
            var pagesSpan = document.createElement('span');
            pagesSpan.className = 'd-inline-flex gap-1 align-items-center';

            nav.appendChild(prevBtn);
            nav.appendChild(pagesSpan);
            nav.appendChild(nextBtn);

            controls.appendChild(infoSpan);
            controls.appendChild(nav);
            container.appendChild(controls);

            function renderPage(page) {
                currentPage = page;
                var startIndex = (currentPage - 1) * pageSize;
                var endIndex = startIndex + pageSize;

                items.forEach(function(item, idx) {
                    if (idx >= startIndex && idx < endIndex) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });

                var visibleEnd = Math.min(endIndex, totalItems);
                infoSpan.textContent = (startIndex + 1) + '-' + visibleEnd + ' dari ' + totalItems;

                prevBtn.disabled = (currentPage === 1);
                nextBtn.disabled = (currentPage === totalPages);

                // Render page buttons
                pagesSpan.innerHTML = '';
                for (var p = 1; p <= totalPages; p++) {
                    var pBtn = document.createElement('button');
                    pBtn.type = 'button';
                    pBtn.className = 'ws-page-btn' + (p === currentPage ? ' active' : '');
                    pBtn.textContent = p;
                    (function(targetPage) {
                        pBtn.addEventListener('click', function(e) {
                            e.preventDefault();
                            if (targetPage !== currentPage) {
                                renderPage(targetPage);
                            }
                        });
                    })(p);
                    pagesSpan.appendChild(pBtn);
                }
            }

            prevBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (currentPage > 1) {
                    renderPage(currentPage - 1);
                }
            });

            nextBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (currentPage < totalPages) {
                    renderPage(currentPage + 1);
                }
            });

            container.dataset.wsPaginationInitialized = 'true';
            renderPage(1);
        });
    }

    window.initWsPagination = initWsPagination;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initWsPagination);
    } else {
        initWsPagination();
    }

    // Support dynamic tab switching (Bootstrap tabs)
    document.addEventListener('shown.bs.tab', function() {
        initWsPagination();
    });

    // Universal fix for Bootstrap modals in workspace:
    // Move any modal element to document.body when shown so it is never trapped behind .modal-backdrop
    document.addEventListener('show.bs.modal', function(e) {
        if (e.target && e.target.parentElement !== document.body) {
            document.body.appendChild(e.target);
        }
    });
})();


