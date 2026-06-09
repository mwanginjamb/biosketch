 // ------------------------------------------------------------------
    // SOURCE ROWS
    // ------------------------------------------------------------------
    const sourceWrapper = document.getElementById('education-wrapper');
    const sourceTemplate = document.getElementById('education-template').innerHTML;

    function getMaxSourceIndex() {
        let max = -1;
        sourceWrapper.querySelectorAll('.education-item').forEach(item => {
            const idxAttr = item.getAttribute('data-index');
            if (idxAttr !== null) {
                max = Math.max(max, parseInt(idxAttr, 10));
            } else {
                const input = item.querySelector('input[name*="[source_type]"]');
                if (input && input.name) {
                    const match = input.name.match(/Sources\[(\d+)\]/);
                    if (match) max = Math.max(max, parseInt(match[1], 10));
                }
            }
        });
        return max;
    }

    function getNextSourceIndex() {
        return getMaxSourceIndex() + 1;
    }

    function addSourceRow() {
        const newIndex = getNextSourceIndex();
        const html = sourceTemplate.replace(/__index__/g, newIndex);
        const div = document.createElement('div');
        div.innerHTML = html;
        const newRow = div.firstElementChild;
        // Enable all inputs inside the new row
        newRow.querySelectorAll('input, select, textarea').forEach(el => el.removeAttribute('disabled'));
        newRow.setAttribute('data-index', newIndex);
        sourceWrapper.appendChild(newRow);
        updateSourceRemoveVisibility();
    }

    function updateSourceRemoveVisibility() {
        const rows = sourceWrapper.querySelectorAll('.education-item');
        const alone = rows.length === 1;
        rows.forEach(row => {
            const btn = row.querySelector('.remove-source');
            if (btn) btn.style.visibility = alone ? 'hidden' : 'visible';
        });
    }

    document.getElementById('add-education').addEventListener('click', addSourceRow);

    sourceWrapper.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-source')) {
            if (sourceWrapper.querySelectorAll('.education-item').length > 1) {
                e.target.closest('.education-item').remove();
                updateSourceRemoveVisibility();
            }
        }
    });

    if (!sourceWrapper.querySelector('.education-item')) {
        addSourceRow();
    } else {
        updateSourceRemoveVisibility();
    }