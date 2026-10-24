/**
 * BegawiPengadaan Workspace Smart Table, Pagination & Micro-Interaction Engine
 *
 * 1. Standardized Data Table Engine for `.workspace-content table`:
 *    - Live Search & Multi-Filtering (Status/Category + Date filter)
 *    - Multi-Column Sorting (Click header; Shift+Click for multi-column sort)
 *    - Pagination with Rows-per-Page selector (5, 10, 25, 50, 100, Semua)
 *    - Export to CSV (UTF-8 BOM) & Export to PDF (Official Biro PBJ Lampung Report)
 * 2. Modular Card/List Pagination Engine for `.ws-paginated-container`
 * 3. Bootstrap Tooltip & Modal Body Portal Initializer
 */
(function () {
    'use strict';

    /* ==========================================================================
       PART 1: MODULAR CONTAINER PAGINATION (.ws-paginated-container)
       ========================================================================== */
    function initWsPagination() {
        var containers = document.querySelectorAll('.ws-paginated-container');
        if (!containers || containers.length === 0) return;

        containers.forEach(function (container) {
            if (container.dataset.wsPaginationInitialized === 'true') {
                return;
            }

            // If this container wraps a table that will be handled by Smart Data Table, skip card pagination
            if (container.querySelector('table thead') && !container.querySelector('.ws-paginated-item:not(tr)')) {
                // Let Smart Table handle <tr> pagination if it's a table, or enhance it below
            }

            var pageSize = parseInt(container.getAttribute('data-page-size') || '6', 10);
            if (isNaN(pageSize) || pageSize < 1) pageSize = 6;

            var items = Array.prototype.slice.call(container.querySelectorAll('.ws-paginated-item'));
            // If items are <tr> inside a table that Smart Table will enhance, let Smart Table handle it
            if (items.length > 0 && items[0].tagName === 'TR' && items[0].closest('table')) {
                return;
            }

            var totalItems = items.length;
            var existingControls = container.querySelector('.ws-pagination-controls');
            if (existingControls) {
                existingControls.remove();
            }

            if (totalItems <= pageSize) {
                items.forEach(function (item) {
                    item.style.display = '';
                });
                return;
            }

            var currentPage = 1;

            var controls = document.createElement('div');
            controls.className = 'ws-pagination-controls d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3 pt-2 border-top';

            var leftWrap = document.createElement('div');
            leftWrap.className = 'd-flex align-items-center gap-2';

            var infoSpan = document.createElement('span');
            infoSpan.className = 'ws-page-info small text-muted';

            var sizeSelect = document.createElement('select');
            sizeSelect.className = 'ws-dt-select';
            sizeSelect.title = 'Jumlah item per halaman';
            [4, 6, 8, 12, 24, 999].forEach(function (sz) {
                var opt = document.createElement('option');
                opt.value = String(sz);
                opt.textContent = sz === 999 ? 'Semua' : sz + ' / hal';
                if (sz === pageSize) opt.selected = true;
                sizeSelect.appendChild(opt);
            });

            leftWrap.appendChild(infoSpan);
            leftWrap.appendChild(sizeSelect);

            var nav = document.createElement('div');
            nav.className = 'ws-page-nav d-flex align-items-center gap-1';

            var prevBtn = document.createElement('button');
            prevBtn.type = 'button';
            prevBtn.className = 'ws-page-btn ws-page-btn-nav';
            prevBtn.innerHTML = '<i class="fas fa-chevron-left me-1"></i> Prev';

            var nextBtn = document.createElement('button');
            nextBtn.type = 'button';
            nextBtn.className = 'ws-page-btn ws-page-btn-nav';
            nextBtn.innerHTML = 'Next <i class="fas fa-chevron-right ms-1"></i>';

            var pagesSpan = document.createElement('span');
            pagesSpan.className = 'd-inline-flex gap-1 align-items-center';

            nav.appendChild(prevBtn);
            nav.appendChild(pagesSpan);
            nav.appendChild(nextBtn);

            controls.appendChild(leftWrap);
            controls.appendChild(nav);
            container.appendChild(controls);

            function renderPage(page) {
                var totalPages = Math.max(1, Math.ceil(totalItems / pageSize));
                currentPage = Math.max(1, Math.min(page, totalPages));
                var startIndex = (currentPage - 1) * pageSize;
                var endIndex = startIndex + pageSize;

                items.forEach(function (item, idx) {
                    item.style.display = (idx >= startIndex && idx < endIndex) ? '' : 'none';
                });

                var visibleEnd = Math.min(endIndex, totalItems);
                infoSpan.textContent = (startIndex + 1) + '-' + visibleEnd + ' dari ' + totalItems;

                prevBtn.disabled = (currentPage === 1);
                nextBtn.disabled = (currentPage === totalPages);

                pagesSpan.innerHTML = '';
                for (var p = 1; p <= totalPages; p++) {
                    if (totalPages > 7 && p > 2 && p < totalPages - 1 && Math.abs(p - currentPage) > 1) {
                        if (p === 3 || p === totalPages - 2) {
                            var dots = document.createElement('span');
                            dots.className = 'px-1 text-muted small';
                            dots.textContent = '...';
                            pagesSpan.appendChild(dots);
                        }
                        continue;
                    }
                    var pBtn = document.createElement('button');
                    pBtn.type = 'button';
                    pBtn.className = 'ws-page-btn' + (p === currentPage ? ' active' : '');
                    pBtn.textContent = p;
                    (function (targetPage) {
                        pBtn.addEventListener('click', function (e) {
                            e.preventDefault();
                            if (targetPage !== currentPage) {
                                renderPage(targetPage);
                            }
                        });
                    })(p);
                    pagesSpan.appendChild(pBtn);
                }
            }

            sizeSelect.addEventListener('change', function () {
                pageSize = parseInt(sizeSelect.value, 10) || 6;
                renderPage(1);
            });

            prevBtn.addEventListener('click', function (e) {
                e.preventDefault();
                if (currentPage > 1) renderPage(currentPage - 1);
            });

            nextBtn.addEventListener('click', function (e) {
                e.preventDefault();
                renderPage(currentPage + 1);
            });

            container.dataset.wsPaginationInitialized = 'true';
            renderPage(1);
        });
    }

    /* ==========================================================================
       PART 2: SMART DATA TABLE ENGINE (Search, Multi-Filter, Multi-Sort, Export)
       ========================================================================== */
    function cleanCellText(cell) {
        if (!cell) return '';
        var clone = cell.cloneNode(true);
        var ignored = clone.querySelectorAll('button, form, .dropdown-menu, script, style, .no-export');
        ignored.forEach(function (el) { el.remove(); });
        return (clone.textContent || '').replace(/\s+/g, ' ').trim();
    }

    function parseSortableValue(rawText) {
        var str = (rawText || '').trim();
        if (!str || str === '-') return { type: 'empty', val: '' };

        // Check dd/mm/yyyy or dd-mm-yyyy with optional HH:mm
        var dmy = str.match(/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})(?:\s+(\d{1,2}):(\d{2}))?/);
        if (dmy) {
            var ts = new Date(
                parseInt(dmy[3], 10),
                parseInt(dmy[2], 10) - 1,
                parseInt(dmy[1], 10),
                parseInt(dmy[4] || '0', 10),
                parseInt(dmy[5] || '0', 10)
            ).getTime();
            if (!isNaN(ts)) return { type: 'number', val: ts };
        }

        // Check dd Mmm yyyy (e.g., 24 Okt 2026 or 24 Oct 2026)
        var months = {
            jan: 0, feb: 1, mar: 2, apr: 3, mei: 4, may: 4, jun: 5,
            jul: 6, agu: 7, aug: 7, sep: 8, okt: 9, oct: 9, nov: 10, des: 11, dec: 11
        };
        var dMonY = str.match(/(\d{1,2})\s+([A-Za-z]{3,})\s+(\d{4})(?:[,\s]+(\d{1,2}):(\d{2}))?/);
        if (dMonY) {
            var mKey = dMonY[2].toLowerCase().substring(0, 3);
            if (months[mKey] !== undefined) {
                var ts2 = new Date(
                    parseInt(dMonY[3], 10),
                    months[mKey],
                    parseInt(dMonY[1], 10),
                    parseInt(dMonY[4] || '0', 10),
                    parseInt(dMonY[5] || '0', 10)
                ).getTime();
                if (!isNaN(ts2)) return { type: 'number', val: ts2 };
            }
        }

        // Check numeric / percentage / ID (#123)
        var numCandidate = str.replace(/^[#Rp\s]+/i, '').replace(/[%]/g, '').replace(/\s.*$/, '');
        if (/^-?\d+([.,]\d+)?$/.test(numCandidate)) {
            var numVal = parseFloat(numCandidate.replace(',', '.'));
            if (!isNaN(numVal)) return { type: 'number', val: numVal };
        }

        return { type: 'string', val: str.toLowerCase() };
    }

    function detectTableTitle(table) {
        var card = table.closest('.card, .workspace-panel, .ws-section-card');
        if (card) {
            var heading = card.querySelector('h2, h3, h4, h5, h6, .card-title');
            if (heading && heading.textContent.trim()) {
                return heading.textContent.replace(/\s+/g, ' ').trim();
            }
        }
        var pageHeader = document.querySelector('.ws-page-header h1, .ws-page-header h2, .workspace-breadcrumb h2');
        return pageHeader ? pageHeader.textContent.trim() : 'Data Tabel Workspace';
    }

    function exportTableToCSV(table, headers, visibleRows, exportableCols, title) {
        var csvLines = [];
        var headerRow = exportableCols.map(function (colIdx) {
            return '"' + (headers[colIdx] || '').replace(/"/g, '""') + '"';
        });
        csvLines.push(headerRow.join(','));

        visibleRows.forEach(function (tr) {
            var cells = tr.querySelectorAll('td, th');
            var line = exportableCols.map(function (colIdx) {
                var text = cleanCellText(cells[colIdx]);
                return '"' + text.replace(/"/g, '""') + '"';
            });
            csvLines.push(line.join(','));
        });

        var csvContent = '\uFEFF' + csvLines.join('\r\n');
        var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        var url = URL.createObjectURL(blob);
        var link = document.createElement('a');
        var slug = title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '') || 'workspace-data';
        var dateStr = new Date().toISOString().slice(0, 10);
        link.setAttribute('href', url);
        link.setAttribute('download', slug + '-' + dateStr + '.csv');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        setTimeout(function () { URL.revokeObjectURL(url); }, 1000);
    }

    function exportTableToPDF(table, headers, visibleRows, exportableCols, title) {
        var now = new Date();
        var printedAt = now.toLocaleDateString('id-ID', {
            day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'
        });

        var thHtml = '<th style="width:40px;text-align:center;">No</th>';
        exportableCols.forEach(function (colIdx) {
            thHtml += '<th>' + (headers[colIdx] || '') + '</th>';
        });

        var trHtml = '';
        visibleRows.forEach(function (tr, idx) {
            var cells = tr.querySelectorAll('td, th');
            trHtml += '<tr><td style="text-align:center;">' + (idx + 1) + '</td>';
            exportableCols.forEach(function (colIdx) {
                var txt = cleanCellText(cells[colIdx]);
                var safeTxt = txt.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                trHtml += '<td>' + (safeTxt || '-') + '</td>';
            });
            trHtml += '</tr>';
        });

        var html = '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8">' +
            '<title>Cetak PDF - ' + title + '</title>' +
            '<style>' +
            'body { font-family: Arial, Helvetica, sans-serif; color: #111827; margin: 24px; font-size: 11pt; }' +
            '.kop { text-align: center; border-bottom: 3px double #082f5c; padding-bottom: 12px; margin-bottom: 18px; }' +
            '.kop h4 { margin: 0; font-size: 11pt; font-weight: 700; color: #334155; letter-spacing: 0.5px; text-transform: uppercase; }' +
            '.kop h2 { margin: 4px 0; font-size: 14pt; font-weight: 800; color: #082f5c; text-transform: uppercase; }' +
            '.kop p { margin: 2px 0 0; font-size: 9pt; color: #475569; }' +
            '.meta { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 12px; font-size: 9.5pt; }' +
            '.meta h3 { margin: 0; font-size: 12pt; color: #082f5c; }' +
            'table { width: 100%; border-collapse: collapse; margin-top: 8px; }' +
            'th, td { border: 1px solid #94a3b8; padding: 7px 9px; font-size: 9.5pt; text-align: left; vertical-align: top; }' +
            'th { background: #082f5c; color: #ffffff; font-weight: 700; -webkit-print-color-adjust: exact; print-color-adjust: exact; }' +
            'tr:nth-child(even) td { background: #f8fafc; -webkit-print-color-adjust: exact; print-color-adjust: exact; }' +
            '@media print { @page { size: A4 landscape; margin: 12mm; } body { margin: 0; } }' +
            '</style></head><body>' +
            '<div class="kop">' +
            '<h4>Pemerintah Provinsi Lampung</h4>' +
            '<h2>Biro Pengadaan Barang dan Jasa Setda Provinsi Lampung</h2>' +
            '<p>Portal Ekosistem Pembelajaran &amp; Kinerja BegawiPengadaan &bull; Dicetak pada: ' + printedAt + ' WIB</p>' +
            '</div>' +
            '<div class="meta">' +
            '<div><h3>' + title + '</h3><span>Total Baris Tercetak: <strong>' + visibleRows.length + ' Data</strong></span></div>' +
            '</div>' +
            '<table><thead><tr>' + thHtml + '</tr></thead><tbody>' + trHtml + '</tbody></table>' +
            '<script>window.onload = function(){ setTimeout(function(){ window.print(); }, 250); };<\/script>' +
            '</body></html>';

        var win = window.open('', '_blank');
        if (win) {
            win.document.open();
            win.document.write(html);
            win.document.close();
        }
    }

    function initWsSmartTables() {
        var contentArea = document.querySelector('.workspace-content');
        if (!contentArea) return;

        var tables = contentArea.querySelectorAll('table');
        tables.forEach(function (table) {
            if (table.dataset.wsSmartTableInitialized === 'true') return;
            if (table.classList.contains('ws-no-datatable') || table.classList.contains('ws-print-table')) return;

            var thead = table.querySelector('thead');
            var tbody = table.querySelector('tbody');
            if (!thead || !tbody) return;

            var headerCells = Array.prototype.slice.call(thead.querySelectorAll('tr:first-child > th, tr:first-child > td'));
            if (headerCells.length < 2) return;

            var allBodyRows = Array.prototype.slice.call(tbody.querySelectorAll(':scope > tr'));
            // Filter out single "empty state" row that spans all columns
            var dataRows = allBodyRows.filter(function (tr) {
                var tds = tr.querySelectorAll('td');
                if (tds.length === 1 && parseInt(tds[0].getAttribute('colspan') || '1', 10) > 1) {
                    return false;
                }
                return tds.length > 0;
            });

            if (dataRows.length === 0) {
                table.dataset.wsSmartTableInitialized = 'true';
                return;
            }

            table.dataset.wsSmartTableInitialized = 'true';

            var headers = headerCells.map(function (th) {
                return (th.textContent || '').replace(/\s+/g, ' ').trim();
            });

            var exportableCols = [];
            headerCells.forEach(function (th, idx) {
                var label = headers[idx].toLowerCase();
                var hasCheckbox = !!th.querySelector('input[type="checkbox"]');
                if (!hasCheckbox && label !== 'aksi' && label !== 'aksi cepat' && label !== 'tindakan' && label !== 'action' && label !== '') {
                    exportableCols.push(idx);
                }
            });

            // Collect distinct badge/status values for auto-filter
            var statusSet = {};
            dataRows.forEach(function (tr) {
                var badges = tr.querySelectorAll('.badge');
                badges.forEach(function (b) {
                    var txt = (b.textContent || '').replace(/\s+/g, ' ').trim();
                    if (txt && txt.length >= 2 && txt.length <= 32 && !/^\d+$/.test(txt) && !/^#/.test(txt)) {
                        statusSet[txt] = (statusSet[txt] || 0) + 1;
                    }
                });
            });
            var statusOptions = Object.keys(statusSet).sort();

            var defaultPageSize = parseInt(table.getAttribute('data-page-size') || '10', 10) || 10;
            var state = {
                search: '',
                statusFilter: '',
                pageSize: defaultPageSize,
                currentPage: 1,
                sortStack: [] // [{ colIdx: number, dir: 'asc'|'desc' }]
            };

            // Store original index on each row for stable reset
            dataRows.forEach(function (tr, i) {
                tr.dataset.wsOrigIndex = String(i);
            });

            // Setup Sortable Headers
            headerCells.forEach(function (th, colIdx) {
                if (exportableCols.indexOf(colIdx) === -1) return;
                th.classList.add('ws-sortable-th');
                th.setAttribute('title', 'Klik untuk mengurutkan kolom (Shift + Klik untuk multi-kolom)');

                var icon = document.createElement('i');
                icon.className = 'fas fa-sort ws-sort-icon';
                th.appendChild(icon);

                var orderBadge = document.createElement('span');
                orderBadge.className = 'ws-sort-order-badge d-none';
                th.appendChild(orderBadge);

                th.addEventListener('click', function (ev) {
                    if (ev.target && ev.target.closest('input, button, a, select')) return;
                    var existingIdx = -1;
                    for (var i = 0; i < state.sortStack.length; i++) {
                        if (state.sortStack[i].colIdx === colIdx) {
                            existingIdx = i;
                            break;
                        }
                    }

                    if (ev.shiftKey) {
                        // Multi-column sort
                        if (existingIdx === -1) {
                            state.sortStack.push({ colIdx: colIdx, dir: 'asc' });
                        } else if (state.sortStack[existingIdx].dir === 'asc') {
                            state.sortStack[existingIdx].dir = 'desc';
                        } else {
                            state.sortStack.splice(existingIdx, 1);
                        }
                    } else {
                        // Single-column sort
                        if (existingIdx !== -1 && state.sortStack.length === 1) {
                            if (state.sortStack[0].dir === 'asc') {
                                state.sortStack[0].dir = 'desc';
                            } else {
                                state.sortStack = [];
                            }
                        } else {
                            state.sortStack = [{ colIdx: colIdx, dir: 'asc' }];
                        }
                    }

                    updateSortHeaderUI();
                    state.currentPage = 1;
                    refreshTable();
                });
            });

            function updateSortHeaderUI() {
                headerCells.forEach(function (th, colIdx) {
                    var icon = th.querySelector('.ws-sort-icon');
                    var badge = th.querySelector('.ws-sort-order-badge');
                    if (!icon) return;

                    var entry = null;
                    var orderNum = 0;
                    for (var i = 0; i < state.sortStack.length; i++) {
                        if (state.sortStack[i].colIdx === colIdx) {
                            entry = state.sortStack[i];
                            orderNum = i + 1;
                            break;
                        }
                    }

                    if (!entry) {
                        th.removeAttribute('data-sort-dir');
                        icon.className = 'fas fa-sort ws-sort-icon';
                        if (badge) badge.classList.add('d-none');
                    } else {
                        th.setAttribute('data-sort-dir', entry.dir);
                        icon.className = entry.dir === 'asc'
                            ? 'fas fa-sort-up ws-sort-icon'
                            : 'fas fa-sort-down ws-sort-icon';
                        if (badge) {
                            if (state.sortStack.length > 1) {
                                badge.textContent = String(orderNum);
                                badge.classList.remove('d-none');
                            } else {
                                badge.classList.add('d-none');
                            }
                        }
                    }
                });
            }

            // Build Toolbar
            var toolbar = document.createElement('div');
            toolbar.className = 'ws-dt-toolbar no-print';

            var leftBar = document.createElement('div');
            leftBar.className = 'ws-dt-toolbar-left';

            var searchWrap = document.createElement('div');
            searchWrap.className = 'ws-dt-search-wrap';
            searchWrap.innerHTML = '<i class="fas fa-search"></i>';
            var searchInput = document.createElement('input');
            searchInput.type = 'search';
            searchInput.className = 'ws-dt-search-input';
            searchInput.placeholder = 'Cari di tabel ini...';
            searchWrap.appendChild(searchInput);
            leftBar.appendChild(searchWrap);

            var statusSelect = null;
            if (statusOptions.length > 1) {
                statusSelect = document.createElement('select');
                statusSelect.className = 'ws-dt-select';
                statusSelect.title = 'Filter berdasarkan Status / Kategori';
                var defaultOpt = document.createElement('option');
                defaultOpt.value = '';
                defaultOpt.textContent = 'Semua Status / Kategori';
                statusSelect.appendChild(defaultOpt);

                statusOptions.forEach(function (st) {
                    var opt = document.createElement('option');
                    opt.value = st.toLowerCase();
                    opt.textContent = st + ' (' + statusSet[st] + ')';
                    statusSelect.appendChild(opt);
                });
                leftBar.appendChild(statusSelect);
            }

            var rightBar = document.createElement('div');
            rightBar.className = 'ws-dt-toolbar-right';

            var pageSizeSelect = document.createElement('select');
            pageSizeSelect.className = 'ws-dt-select';
            pageSizeSelect.title = 'Jumlah baris per halaman';
            [5, 10, 25, 50, 100, 9999].forEach(function (sz) {
                var opt = document.createElement('option');
                opt.value = String(sz);
                opt.textContent = sz === 9999 ? 'Semua Baris' : sz + ' baris';
                if (sz === defaultPageSize) opt.selected = true;
                pageSizeSelect.appendChild(opt);
            });
            rightBar.appendChild(pageSizeSelect);

            var csvBtn = document.createElement('button');
            csvBtn.type = 'button';
            csvBtn.className = 'ws-dt-export-btn ws-dt-export-btn--csv';
            csvBtn.title = 'Unduh data tabel dalam format CSV (Excel)';
            csvBtn.innerHTML = '<i class="fas fa-file-csv text-success"></i> <span>CSV</span>';
            rightBar.appendChild(csvBtn);

            var pdfBtn = document.createElement('button');
            pdfBtn.type = 'button';
            pdfBtn.className = 'ws-dt-export-btn ws-dt-export-btn--pdf';
            pdfBtn.title = 'Ekspor / Cetak data tabel ke PDF resmi';
            pdfBtn.innerHTML = '<i class="fas fa-file-pdf text-danger"></i> <span>PDF</span>';
            rightBar.appendChild(pdfBtn);

            toolbar.appendChild(leftBar);
            toolbar.appendChild(rightBar);

            // Insert Toolbar before table or its .table-responsive wrapper
            var tableContainer = table.closest('.table-responsive') || table;
            tableContainer.parentNode.insertBefore(toolbar, tableContainer);

            // Remove any old static pagination controls inside parent container
            var parentPaginated = table.closest('.ws-paginated-container');
            if (parentPaginated) {
                var oldCtrl = parentPaginated.querySelector('.ws-pagination-controls');
                if (oldCtrl) oldCtrl.remove();
                parentPaginated.dataset.wsPaginationInitialized = 'true';
            }

            // Build Footer Pagination Bar
            var footer = document.createElement('div');
            footer.className = 'ws-dt-footer no-print';

            var infoEl = document.createElement('span');
            infoEl.className = 'ws-page-info small text-muted fw-medium';

            var navEl = document.createElement('div');
            navEl.className = 'ws-page-nav d-flex align-items-center gap-1';

            var prevBtn = document.createElement('button');
            prevBtn.type = 'button';
            prevBtn.className = 'ws-page-btn ws-page-btn-nav';
            prevBtn.innerHTML = '<i class="fas fa-chevron-left me-1"></i> Prev';

            var pagesWrap = document.createElement('span');
            pagesWrap.className = 'd-inline-flex gap-1 align-items-center';

            var nextBtn = document.createElement('button');
            nextBtn.type = 'button';
            nextBtn.className = 'ws-page-btn ws-page-btn-nav';
            nextBtn.innerHTML = 'Next <i class="fas fa-chevron-right ms-1"></i>';

            navEl.appendChild(prevBtn);
            navEl.appendChild(pagesWrap);
            navEl.appendChild(nextBtn);

            footer.appendChild(infoEl);
            footer.appendChild(navEl);
            tableContainer.parentNode.insertBefore(footer, tableContainer.nextSibling);

            // Empty search result row
            var emptyFilterRow = document.createElement('tr');
            emptyFilterRow.className = 'ws-dt-empty-row';
            emptyFilterRow.style.display = 'none';
            var emptyTd = document.createElement('td');
            emptyTd.colSpan = headerCells.length;
            emptyTd.className = 'text-center py-4 text-muted small';
            emptyTd.innerHTML = '<i class="fas fa-search me-1"></i> Tidak ada baris data yang cocok dengan filter pencarian.';
            emptyFilterRow.appendChild(emptyTd);
            tbody.appendChild(emptyFilterRow);

            var currentFilteredRows = dataRows.slice();

            function refreshTable() {
                var q = state.search.toLowerCase().trim();
                var stFilter = state.statusFilter.toLowerCase().trim();

                // 1. Filter
                currentFilteredRows = dataRows.filter(function (tr) {
                    if (q !== '') {
                        var rowTxt = (tr.textContent || '').toLowerCase();
                        if (rowTxt.indexOf(q) === -1) return false;
                    }
                    if (stFilter !== '') {
                        var matchedBadge = false;
                        var badges = tr.querySelectorAll('.badge');
                        badges.forEach(function (b) {
                            var bTxt = (b.textContent || '').replace(/\s+/g, ' ').trim().toLowerCase();
                            if (bTxt === stFilter) matchedBadge = true;
                        });
                        if (!matchedBadge) return false;
                    }
                    return true;
                });

                // 2. Multi-column Sort
                if (state.sortStack.length > 0) {
                    currentFilteredRows.sort(function (rowA, rowB) {
                        var cellsA = rowA.querySelectorAll('td, th');
                        var cellsB = rowB.querySelectorAll('td, th');

                        for (var s = 0; s < state.sortStack.length; s++) {
                            var sortItem = state.sortStack[s];
                            var cIdx = sortItem.colIdx;
                            var dirMult = sortItem.dir === 'asc' ? 1 : -1;

                            var parsedA = parseSortableValue(cleanCellText(cellsA[cIdx]));
                            var parsedB = parseSortableValue(cleanCellText(cellsB[cIdx]));

                            if (parsedA.val === parsedB.val) continue;
                            if (parsedA.type === 'empty') return 1;
                            if (parsedB.type === 'empty') return -1;

                            if (parsedA.type === 'number' && parsedB.type === 'number') {
                                return (parsedA.val - parsedB.val) * dirMult;
                            }
                            return String(parsedA.val).localeCompare(String(parsedB.val), 'id', { numeric: true }) * dirMult;
                        }
                        return parseInt(rowA.dataset.wsOrigIndex, 10) - parseInt(rowB.dataset.wsOrigIndex, 10);
                    });
                } else {
                    currentFilteredRows.sort(function (rowA, rowB) {
                        return parseInt(rowA.dataset.wsOrigIndex, 10) - parseInt(rowB.dataset.wsOrigIndex, 10);
                    });
                }

                // Re-append in sorted order
                currentFilteredRows.forEach(function (tr) {
                    tbody.insertBefore(tr, emptyFilterRow);
                });

                // Hide all first
                dataRows.forEach(function (tr) {
                    tr.style.display = 'none';
                });

                var totalFiltered = currentFilteredRows.length;
                if (totalFiltered === 0) {
                    emptyFilterRow.style.display = '';
                    infoEl.textContent = 'Menampilkan 0 dari ' + dataRows.length + ' data';
                    prevBtn.disabled = true;
                    nextBtn.disabled = true;
                    pagesWrap.innerHTML = '';
                    return;
                }

                emptyFilterRow.style.display = 'none';

                var totalPages = Math.max(1, Math.ceil(totalFiltered / state.pageSize));
                if (state.currentPage > totalPages) state.currentPage = totalPages;
                if (state.currentPage < 1) state.currentPage = 1;

                var startIdx = (state.currentPage - 1) * state.pageSize;
                var endIdx = Math.min(startIdx + state.pageSize, totalFiltered);

                for (var i = startIdx; i < endIdx; i++) {
                    currentFilteredRows[i].style.display = '';
                }

                infoEl.textContent = 'Menampilkan ' + (startIdx + 1) + '–' + endIdx + ' dari ' + totalFiltered + ' data' +
                    (totalFiltered < dataRows.length ? ' (difilter dari ' + dataRows.length + ')' : '');

                prevBtn.disabled = (state.currentPage <= 1);
                nextBtn.disabled = (state.currentPage >= totalPages);

                pagesWrap.innerHTML = '';
                for (var p = 1; p <= totalPages; p++) {
                    if (totalPages > 7 && p > 2 && p < totalPages - 1 && Math.abs(p - state.currentPage) > 1) {
                        if (p === 3 || p === totalPages - 2) {
                            var ellipsis = document.createElement('span');
                            ellipsis.className = 'px-1 text-muted small';
                            ellipsis.textContent = '...';
                            pagesWrap.appendChild(ellipsis);
                        }
                        continue;
                    }
                    var btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'ws-page-btn' + (p === state.currentPage ? ' active' : '');
                    btn.textContent = String(p);
                    (function (targetP) {
                        btn.addEventListener('click', function (ev) {
                            ev.preventDefault();
                            state.currentPage = targetP;
                            refreshTable();
                        });
                    })(p);
                    pagesWrap.appendChild(btn);
                }
            }

            // Events
            searchInput.addEventListener('input', function () {
                state.search = searchInput.value;
                state.currentPage = 1;
                refreshTable();
            });

            if (statusSelect) {
                statusSelect.addEventListener('change', function () {
                    state.statusFilter = statusSelect.value;
                    state.currentPage = 1;
                    refreshTable();
                });
            }

            pageSizeSelect.addEventListener('change', function () {
                state.pageSize = parseInt(pageSizeSelect.value, 10) || 10;
                state.currentPage = 1;
                refreshTable();
            });

            prevBtn.addEventListener('click', function (ev) {
                ev.preventDefault();
                if (state.currentPage > 1) {
                    state.currentPage--;
                    refreshTable();
                }
            });

            nextBtn.addEventListener('click', function (ev) {
                ev.preventDefault();
                state.currentPage++;
                refreshTable();
            });

            csvBtn.addEventListener('click', function () {
                var title = detectTableTitle(table);
                exportTableToCSV(table, headers, currentFilteredRows, exportableCols, title);
            });

            pdfBtn.addEventListener('click', function () {
                var title = detectTableTitle(table);
                exportTableToPDF(table, headers, currentFilteredRows, exportableCols, title);
            });

            refreshTable();
        });
    }

    /* ==========================================================================
       PART 3: TOOLTIPS & MODAL FIXES
       ========================================================================== */
    function initWsTooltips() {
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function (tooltipTriggerEl) {
                if (!bootstrap.Tooltip.getInstance(tooltipTriggerEl)) {
                    new bootstrap.Tooltip(tooltipTriggerEl);
                }
            });
        }
    }

    function bootWorkspaceEnhancements() {
        initWsSmartTables();
        initWsPagination();
        initWsTooltips();
    }

    window.initWsPagination = bootWorkspaceEnhancements;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootWorkspaceEnhancements);
    } else {
        bootWorkspaceEnhancements();
    }

    // Re-init on Bootstrap tab switches
    document.addEventListener('shown.bs.tab', function () {
        bootWorkspaceEnhancements();
    });

    // Universal fix for Bootstrap modals in workspace:
    // Move any modal element to document.body when shown so it is never trapped behind .modal-backdrop
    document.addEventListener('show.bs.modal', function (e) {
        if (e.target && e.target.parentElement !== document.body) {
            document.body.appendChild(e.target);
        }
    });
})();
