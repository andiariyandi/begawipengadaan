/**
 * BegawiPengadaan Advanced DataTable Engine
 * Integrated for WowDash Admin Dashboard & DataTables 2.x
 * 
 * Features:
 * - Native DataTables 2.x Layout Engine (seamless WowDash integration)
 * - Multi-column sorting with visual indicators
 * - Instant search with 300ms debounce
 * - Preset page lengths: 10, 50, 100, 500
 * - UTF-8 BOM CSV Export (Excel Compatible)
 * - Bulk Checkbox Selection & Action Toolbar
 * - External Facet dropdown filter integration
 * - Graceful Empty State handling (Zero Tech Note 4 errors)
 * - Full Dark & Light mode compatibility
 */

(function ($) {
  'use strict';

  // 0. Suppress intrusive browser alert popups from DataTables globally
  if (typeof $ !== 'undefined' && $.fn && $.fn.dataTable && $.fn.dataTable.ext) {
    $.fn.dataTable.ext.errMode = function (settings, helpPage, message) {
      console.warn('Begawi DataTable warning handled:', message, '(Help: ' + helpPage + ')');
    };
  }

  // Export CSV Function with UTF-8 BOM
  function exportDataTableToCSV(tableApi, tableName) {
    if (!tableApi) return;

    var headers = [];
    var colIndexes = [];
    
    // 1. Gather visible & exportable column headers
    tableApi.columns().every(function (idx) {
      var headerNode = $(this.header());
      if (
        !headerNode.hasClass('no-export') &&
        !headerNode.hasClass('dt-no-export') &&
        !headerNode.hasClass('action-col') &&
        headerNode.text().trim() !== '' &&
        headerNode.find('input[type="checkbox"]').length === 0
      ) {
        headers.push('"' + headerNode.text().trim().replace(/"/g, '""') + '"');
        colIndexes.push(idx);
      }
    });

    if (colIndexes.length === 0) {
      alert('Tidak ada kolom yang dapat diekspor.');
      return;
    }

    var csvRows = [];
    csvRows.push(headers.join(','));

    // 2. Extract filtered row data
    tableApi.rows({ search: 'applied' }).every(function () {
      var rowNode = this.node();
      var rowData = [];

      for (var i = 0; i < colIndexes.length; i++) {
        var colIdx = colIndexes[i];
        var cell = $(rowNode).find('td').eq(colIdx);
        var text = '';

        if (cell.length) {
          // Clone to strip badges or hidden icons cleanly
          var clone = cell.clone();
          clone.find('script, style, button, .dropdown, iconify-icon, .no-export').remove();
          text = clone.text().replace(/\s+/g, ' ').trim();
        } else {
          var rawVal = tableApi.cell(this.index(), colIdx).data();
          text = rawVal ? $('<div>').html(rawVal).text().trim() : '';
        }

        rowData.push('"' + text.replace(/"/g, '""') + '"');
      }

      csvRows.push(rowData.join(','));
    });

    // 3. Prepend UTF-8 BOM (\uFEFF)
    var csvContent = '\uFEFF' + csvRows.join('\r\n');
    var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    var url = URL.createObjectURL(blob);

    var now = new Date();
    var timestamp = now.getFullYear() +
      String(now.getMonth() + 1).padStart(2, '0') +
      String(now.getDate()).padStart(2, '0') + '-' +
      String(now.getHours()).padStart(2, '0') +
      String(now.getMinutes()).padStart(2, '0') +
      String(now.getSeconds()).padStart(2, '0');

    var safeName = (tableName || 'data-ekspor')
      .toLowerCase()
      .replace(/[^a-z0-9_-]/g, '-')
      .replace(/-+/g, '-');
    var fileName = safeName + '-' + timestamp + '.csv';

    var link = document.createElement('a');
    link.setAttribute('href', url);
    link.setAttribute('download', fileName);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
  }

  // Initialize a Single Table with Begawi Standard
  function initTable(tableElement, userOptions) {
    var $table = $(tableElement);
    if ($table.hasClass('dataTable') || ($.fn.DataTable && $.fn.DataTable.isDataTable($table))) {
      return; // Already initialized
    }

    // Skip client-side DataTables initialization for tables that use backend server-side pagination,
    // sorting, and filtering (prevents double pagination, broken search on next page, and sorting override).
    if ($table.data('server-side') === true ||
        $table.data('server-side') === 'true' ||
        $table.attr('data-server-side') !== undefined ||
        $table.hasClass('dt-server-side') ||
        $table.hasClass('server-side-table')) {
      return;
    }

    // If table is directly wrapped in .table-responsive, unwrap it so DataTables 2 layout controls
    // (search box, length dropdown, pagination) are not trapped inside the table horizontal scroll area
    if ($table.parent().hasClass('table-responsive')) {
      $table.unwrap();
    }

    var tableName = $table.data('table-name') || $table.attr('id') || 'begawi-data';
    var defaultPageLength = parseInt($table.data('page-length'), 10) || 10;
    var disableSortLast = $table.data('disable-sort-last') !== false;

    // Determine non-sortable columns
    var colDefs = [];
    if (disableSortLast) {
      colDefs.push({ orderable: false, targets: -1 }); // Action column
    }
    colDefs.push({ orderable: false, targets: 'no-sort' });

    // Handle empty state placeholder rows with colspan before DataTables initializes.
    // If tbody contains only placeholder rows with colspan (e.g., <td colspan="8">Belum ada data...</td>),
    // DataTables DOM parser would fail with Tech Note 4 because the row doesn't have matching number of columns.
    var $tbody = $table.children('tbody');
    if (!$tbody.length) {
      $tbody = $table.find('tbody');
    }
    var $rows = $tbody.children('tr');
    var customEmptyMsg = null;

    if ($rows.length > 0) {
      var isOnlyPlaceholder = true;
      $rows.each(function () {
        var $tds = $(this).children('td');
        if ($tds.length === 1 && ($tds.attr('colspan') || $tds.hasClass('dataTables_empty') || $tds.hasClass('text-muted') || $tds.hasClass('text-secondary-light'))) {
          // Placeholder row
        } else {
          isOnlyPlaceholder = false;
          return false;
        }
      });

      if (isOnlyPlaceholder) {
        customEmptyMsg = $rows.first().text().replace(/\s+/g, ' ').trim();
        $tbody.empty(); // Remove placeholder row so DataTables handles the empty state natively
      }
    }

    var dtOptions = $.extend(true, {
      pageLength: defaultPageLength,
      lengthMenu: [
        [10, 50, 100, 500, -1],
        ['10 Baris', '50 Baris', '100 Baris', '500 Baris', 'Semua Data']
      ],
      autoWidth: false,
      order: [],
      columnDefs: colDefs,
      layout: {
        topStart: 'pageLength',
        topEnd: {
          search: {
            placeholder: 'Cari data...'
          }
        },
        bottomStart: 'info',
        bottomEnd: 'paging'
      },
      language: {
        search: '',
        searchPlaceholder: 'Cari data...',
        lengthMenu: 'Tampilkan _MENU_',
        info: 'Menampilkan _START_ s/d _END_ dari _TOTAL_ data',
        infoEmpty: customEmptyMsg || 'Tidak ada data',
        emptyTable: customEmptyMsg || 'Tidak ada data yang tersedia pada tabel ini',
        infoFiltered: '(disaring dari _MAX_ entri)',
        zeroRecords: customEmptyMsg || 'Tidak ditemukan data yang sesuai',
        paginate: {
          first: '<i class="ri-arrow-left-double-line"></i>',
          last: '<i class="ri-arrow-right-double-line"></i>',
          next: '<i class="ri-arrow-right-s-line"></i>',
          previous: '<i class="ri-arrow-left-s-line"></i>'
        }
      },
      initComplete: function (settings, json) {
        var api = this.api();
        var $container = $table.closest('.dt-container');

        // Add CSV Export Button inside dt-layout-cell.dt-start (beside length menu)
        var $topStartCell = $container.find('.dt-layout-row:first-child .dt-layout-cell.dt-start');
        if ($topStartCell.length && !$topStartCell.find('.btn-export-csv').length) {
          var $exportBtn = $(
            '<button type="button" class="btn btn-outline-success-600 radius-8 px-14 py-8 text-xs fw-semibold d-inline-flex align-items-center gap-1 btn-export-csv shadow-none ms-2">' +
              '<iconify-icon icon="solar:download-square-outline" class="text-base"></iconify-icon>' +
              '<span>Ekspor CSV</span>' +
            '</button>'
          );

          $exportBtn.on('click', function () {
            exportDataTableToCSV(api, tableName);
          });

          $topStartCell.append($exportBtn);
        }

        // Connect External Dropdown Filters
        var tableId = $table.attr('id');
        if (tableId) {
          $('select[data-dt-target="#' + tableId + '"], select[data-dt-target="' + tableId + '"]').each(function () {
            var $filterSelect = $(this);
            var colIndex = parseInt($filterSelect.data('dt-col'), 10);
            if (!isNaN(colIndex)) {
              $filterSelect.on('change', function () {
                var val = $(this).val();
                api.column(colIndex).search(val ? '^' + $.fn.dataTable.util.escapeRegex(val) + '$' : '', true, false).draw();
              });
            }
          });
        }

        // Bulk Checkbox Handling
        var $checkAll = $table.find('thead th input[type="checkbox"].dt-check-all, thead th input[type="checkbox"].check-all');
        if ($checkAll.length) {
          $checkAll.on('change', function () {
            var isChecked = $(this).prop('checked');
            $table.find('tbody td input[type="checkbox"]').prop('checked', isChecked).trigger('change');
          });

          $table.on('change', 'tbody td input[type="checkbox"]', function () {
            var total = $table.find('tbody td input[type="checkbox"]').length;
            var checkedCount = $table.find('tbody td input[type="checkbox"]:checked').length;
            $checkAll.prop('checked', total > 0 && total === checkedCount);

            // Update bulk action toolbar if exists
            var $bulkBar = $('.dt-bulk-toolbar[data-for="' + tableId + '"], #dtBulkToolbar');
            if ($bulkBar.length) {
              if (checkedCount > 0) {
                $bulkBar.removeClass('d-none').addClass('d-flex');
                $bulkBar.find('.dt-selected-count').text(checkedCount);
              } else {
                $bulkBar.removeClass('d-flex').addClass('d-none');
              }
            }
          });
        }
      }
    }, userOptions || {});

    var dataTableInstance = $table.DataTable(dtOptions);

    // Search Debounce (300ms)
    var searchTimer = null;
    $table.closest('.dt-container').find('.dt-search input').unbind().bind('input', function (e) {
      var val = this.value;
      clearTimeout(searchTimer);
      searchTimer = setTimeout(function () {
        dataTableInstance.search(val).draw();
      }, 300);
    });

    return dataTableInstance;
  }

  // Global exposure
  window.initBegawiDataTable = initTable;
  window.exportBegawiTableCSV = exportDataTableToCSV;

  // Auto-initialize all tables with class .begawi-table on DOM ready
  $(document).ready(function () {
    $('.begawi-table').not('[data-server-side="true"]').not('.server-side-table').not('.dt-server-side').each(function () {
      initTable(this);
    });
  });

})(jQuery);
