var usersTable = document.getElementById('users-table');

// Pagination
var paginationInfo = {
    "current-page": Math.max(Number(new URLSearchParams(window.location.search).get('page')), 1),
    "total-pages": 0,
    "current-users": 0,
    "total-users": 0,
    "offset": 0
};
var paginationText = document.getElementById('pagination');
for (let button of paginationText.parentElement.getElementsByTagName('button')) {
    if (button.onclick.toString().search(/ChangePage\(-1\)/) != -1) {
        var paginationLeftButton = button;
    }
    else if (button.onclick.toString().search(/ChangePage\(1\)/) != -1) {
        var paginationRightButton = button;
    }
}

function UpdatePaginationText() {
    paginationText.innerHTML = `Showing users <span class="num">` + (1 + paginationInfo['offset']) + ` - ` + (paginationInfo['current-users'] + paginationInfo['offset']) + `</span> of <span class="num">` + paginationInfo['total-users'] + `</span>
		<span class="page">Page ` + paginationInfo['current-page'] + ` of ` + paginationInfo['total-pages'] + `</span>`;
    
    if (paginationInfo['current-page'] <= 1)
        paginationLeftButton.disabled = true;
    else
        paginationLeftButton.disabled = false;
    
    if (paginationInfo['current-page'] >= paginationInfo['total-pages'])
        paginationRightButton.disabled = true;
    else
        paginationRightButton.disabled = false;
}

function ChangePage(value, setTo) {
    // Update page value
    if (!setTo) {
        if (value > 0)
            paginationInfo['current-page'] = Math.min(paginationInfo['current-page'] + value, paginationInfo['total-pages']);
        else if (value < 0)
            paginationInfo['current-page'] = Math.max(paginationInfo['current-page'] + value, 1);
    }
    else {
        paginationInfo['current-page'] = value;
    }

    // Update URL
    let newUrl = new URL(window.location.href);
    newUrl.searchParams.set('page', paginationInfo['current-page']);

    window.location.href = newUrl;
}

// Changing number of visible rows
function UpdateNumOfRows(event) {
    document.cookie = 'users_table_num_of_rows=' + event.target.value;

    ChangePage(1, true);

    UpdateUsersTable();
}

async function UpdateUsersTable() {
    let formData = new FormData();
    formData.append('page', paginationInfo['current-page']);

    await fetch(window.location.origin + "/information_system/website/operations/user/load_users_table", {
        method: 'POST',
        body: formData
    })
    .then((response) => response.text())
    .then((data) => {
        // Get pagination info
        let dataSplit = data.split(/!!!pagination:/);
        let data_paginationInfo = JSON.parse(dataSplit[1]);
        paginationInfo['total-pages'] = data_paginationInfo['total_pages'];
        paginationInfo['current-users'] = data_paginationInfo['current_users'];
        paginationInfo['total-users'] = data_paginationInfo['total_users'];
        paginationInfo['offset'] = data_paginationInfo['offset'];

        // Update table items
        usersTable.outerHTML = dataSplit[0];
        usersTable = document.getElementById('users-table');

        // Update pagination
        UpdatePaginationText();
    });
}
UpdateUsersTable();