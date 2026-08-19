// ------------------------------------------------------------------
// EDUCATION ROWS
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



// ------------------------------------------------------------------
// PUBLICATIONS ROWS
// ------------------------------------------------------------------
const publicationWrapper = document.getElementById('publication-wrapper');
const publicationTemplate = document.getElementById('publication-template').innerHTML;

function getMaxPublicationIndex() {
    let max = -1;
    publicationWrapper.querySelectorAll('.publication-item').forEach(item => {
        const idxAttr = item.getAttribute('data-index');
        if (idxAttr !== null) {
            max = Math.max(max, parseInt(idxAttr, 10));
        } else {
            const input = item.querySelector('input[name*="[publication_type]"]');
            if (input && input.name) {
                const match = input.name.match(/Publications\[(\d+)\]/);
                if (match) max = Math.max(max, parseInt(match[1], 10));
            }
        }
    });
    return max;
}

function getNextPublicationIndex() {
    return getMaxPublicationIndex() + 1;
}

function addPublicationRow() {
    const newIndex = getNextPublicationIndex();
    const html = publicationTemplate.replace(/__index__/g, newIndex);
    const div = document.createElement('div');
    div.innerHTML = html;
    const newRow = div.firstElementChild;
    // Enable all inputs inside the new row
    newRow.querySelectorAll('input, select, textarea').forEach(el => el.removeAttribute('disabled'));
    newRow.setAttribute('data-index', newIndex);
    publicationWrapper.appendChild(newRow);
    updatePublicationRemoveVisibility();
}

function updatePublicationRemoveVisibility() {
    const rows = publicationWrapper.querySelectorAll('.publication-item');
    const alone = rows.length === 1;
    rows.forEach(row => {
        const btn = row.querySelector('.remove-publication');
        if (btn) btn.style.visibility = alone ? 'hidden' : 'visible';
    });
}

document.getElementById('add-publication').addEventListener('click', addPublicationRow);

publicationWrapper.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-publication')) {
        if (publicationWrapper.querySelectorAll('.publication-item').length > 1) {
            e.target.closest('.publication-item').remove();
            updatePublicationRemoveVisibility();
        }
    }
});

if (!publicationWrapper.querySelector('.publication-item')) {
    addPublicationRow();
} else {
    updatePublicationRemoveVisibility();
}


// ------------------------------------------------------------------
// IDENTIFIER ROWS
// ------------------------------------------------------------------
const identifierWrapper = document.getElementById('identifier-wrapper');
const identifierTemplate = document.getElementById('identifier-template').innerHTML;

function getMaxIdentifierIndex() {
    let max = -1;
    identifierWrapper.querySelectorAll('.identifier-item').forEach(item => {
        const idxAttr = item.getAttribute('data-index');
        if (idxAttr !== null) {
            max = Math.max(max, parseInt(idxAttr, 10));
        } else {
            const input = item.querySelector('input[name*="[identifier_type]"]');
            if (input && input.name) {
                const match = input.name.match(/Identifiers\[(\d+)\]/);
                if (match) max = Math.max(max, parseInt(match[1], 10));
            }
        }
    });
    return max;
}

function getNextIdentifierIndex() {
    return getMaxIdentifierIndex() + 1;
}

function addIdentifierRow() {
    const newIndex = getNextIdentifierIndex();
    const html = identifierTemplate.replace(/__index__/g, newIndex);
    const div = document.createElement('div');
    div.innerHTML = html;
    const newRow = div.firstElementChild;
    // Enable all inputs inside the new row
    newRow.querySelectorAll('input, select, textarea').forEach(el => el.removeAttribute('disabled'));
    newRow.setAttribute('data-index', newIndex);
    identifierWrapper.appendChild(newRow);
    updateIdentifierRemoveVisibility();
}

function updateIdentifierRemoveVisibility() {
    const rows = identifierWrapper.querySelectorAll('.identifier-item');
    const alone = rows.length === 1;
    rows.forEach(row => {
        const btn = row.querySelector('.remove-identifier');
        if (btn) btn.style.visibility = alone ? 'hidden' : 'visible';
    });
}

document.getElementById('add-identifier').addEventListener('click', addIdentifierRow);

identifierWrapper.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-identifier')) {
        if (identifierWrapper.querySelectorAll('.identifier-item').length > 1) {
            e.target.closest('.identifier-item').remove();
            updateIdentifierRemoveVisibility();
        }

    }
});

if (!identifierWrapper.querySelector('.identifier-item')) {
    addIdentifierRow();
} else {
    updateIdentifierRemoveVisibility();
}

// ------------------------------------------------------------------
// GRANT ROWS
// ------------------------------------------------------------------
const grantWrapper = document.getElementById('grants-wrapper');
const grantTemplate = document.getElementById('grant-template').innerHTML;

function getMaxGrantIndex() {
    let max = -1;
    grantWrapper.querySelectorAll('.grant-item').forEach(item => {
        const idxAttr = item.getAttribute('data-index');
        if (idxAttr !== null) {
            max = Math.max(max, parseInt(idxAttr, 10));
        } else {
            const input = item.querySelector('input[name*="[grant_type]"]');
            if (input && input.name) {
                const match = input.name.match(/ResearcherGrant\[(\d+)\]/);
                if (match) max = Math.max(max, parseInt(match[1], 10));
            }
        }
    });
    return max;
}

function getNextGrantIndex() {
    return getMaxGrantIndex() + 1;
}

function addGrantRow() {
    const newIndex = getNextGrantIndex();
    const html = grantTemplate.replace(/__index__/g, newIndex);
    const div = document.createElement('div');
    div.innerHTML = html;
    const newRow = div.firstElementChild;
    // Enable all inputs inside the new row
    newRow.querySelectorAll('input, select, textarea').forEach(el => el.removeAttribute('disabled'));
    newRow.setAttribute('data-index', newIndex);
    grantWrapper.appendChild(newRow);
    updateGrantRemoveVisibility();
}

function updateGrantRemoveVisibility() {
    const rows = grantWrapper.querySelectorAll('.grant-item');
    const alone = rows.length === 1;
    rows.forEach(row => {
        const btn = row.querySelector('.remove-grant');
        if (btn) btn.style.visibility = alone ? 'hidden' : 'visible';
    });
}

document.getElementById('add-grant').addEventListener('click', addGrantRow);

grantWrapper.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-grant')) {
        if (grantWrapper.querySelectorAll('.grant-item').length > 1) {
            e.target.closest('.grant-item').remove();
            updateGrantRemoveVisibility();
        }

    }
});

if (!grantWrapper.querySelector('.grant-item')) {
    addGrantRow();
} else {
    updateGrantRemoveVisibility();
}

