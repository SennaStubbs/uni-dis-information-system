// Filters
var filterButtons = document.getElementById('filters').getElementsByTagName('button');

// Pie Charts
var hoverDistance = 5; // Pixels
var selectedDistance = 10; // Pixels

// Updating all charts when a new filter is applied
async function FilterUpdate(initialising) {
    // Update cookie
    document.cookie = 'dashboard_rarities=' + selectedRarities.toString();

    // Filter buttons
    let rarityIndex = 0;
    for (let rarity of rarityOrder) {
        if (selectedRarities.includes(rarity))
            filterButtons[rarityIndex].classList.remove('not-selected')
        else
            filterButtons[rarityIndex].classList.add('not-selected')
        rarityIndex++;
    }

    // Pie charts
    for (let pieChart of document.getElementsByClassName('pie-chart')) {
        for (let rarity of rarityOrder) {
            let section = document.getElementById(pieChart.id + '-' + rarity);
            if (section) {
                section.dataset.selected = selectedRarities.includes(rarity);

                Pie_UpdateSection(section);
            }
        }

        if (!pieChart.classList.contains('shadow')) {
            // Update total count
            let formData = new FormData();
            formData.append('rarities', selectedRarities);

            await fetch(window.location.origin + "/information_system/website/operations/dashboard/fetch_" + pieChart.dataset.operation, {
                method: 'POST',
                body: formData
            })
            .then((response) => response.text())
            .then((data) => {
                let totalElement = document.getElementById(pieChart.id + '-total');
                totalElement.innerHTML = data + " " + pieChart.dataset.valueType;

                let hiddenElement = totalElement.parentElement.getElementsByClassName('from-selection')[0];
                if (selectedRarities.length > 0) {
                    hiddenElement.classList.remove('hidden');
                } else {
                    hiddenElement.classList.add('hidden');
                }
            });
        }
    }

    // Bar charts
    for (let barChart of document.getElementsByClassName('bar-chart')) {
        for (let rarity of rarityOrder) {
            let bar = document.getElementById(barChart.id + '-' + rarity);
            if (bar) {
                bar.dataset.selected = selectedRarities.includes(rarity);
            }
        }

        // Update counter
        let formData = new FormData();
        formData.append('rarities', selectedRarities);
        await fetch(window.location.origin + "/information_system/website/operations/dashboard/fetch_" + barChart.dataset.operation, {
            method: 'POST',
            body: formData
        })
        .then((response) => response.text())
        .then((data) => {
            let totalElement = document.getElementById(barChart.id + '-counter');
            totalElement.innerHTML = data + " " + barChart.dataset.valueType;

            let hiddenElement = totalElement.parentElement.getElementsByClassName('from-selection')[0];
            if (selectedRarities.length > 0) {
                hiddenElement.classList.remove('hidden');
            } else {
                hiddenElement.classList.add('hidden');
            }
        });
    }

    // Tables
    for (let table of document.getElementsByTagName('table')) {
        let formData = new FormData();
        formData.append('rarities', selectedRarities);
        await fetch(window.location.origin + "/information_system/website/operations/dashboard/fetch_high_value_items", {
            method: 'POST',
            body: formData
        })
        .then((response) => response.text())
        .then((data) => {
            table.outerHTML = data;
        });
    }
}
FilterUpdate();

// Selecting a rarity to filter by
function SelectRarityFilter(rarity, section) {
    if (section) {
        section.dataset.selected = !(section.dataset.selected === 'true');
        rarity = section.dataset.rarity;

        if (!selectedRarities.includes(rarity) && section.dataset.selected == 'true')
            selectedRarities.push(rarity);
        else if (selectedRarities.includes(rarity) && section.dataset.selected == 'false')
            selectedRarities.splice(selectedRarities.indexOf(rarity), 1);
    }
    else {
        if (!selectedRarities.includes(rarity))
            selectedRarities.push(rarity);
        else if (selectedRarities.includes(rarity))
            selectedRarities.splice(selectedRarities.indexOf(rarity), 1);
    }

    FilterUpdate();
}

//// Pie charts
// Hovering a section of a pie chart
function Pie_HoverSection(event, section, title, prefix, value, valueType) {
    let pie = section.parentElement;
    if (section.dataset.selected != 'true') {
        let pieWidth = pie.clientWidth;
        let pieHeight = pie.clientHeight;
        let pieCenterX = pieWidth / 2;
        let pieCenterY = pieHeight / 2;

        let endMidX = section.dataset.endMidX;
        let endMidY = section.dataset.endMidY;

        let magnitude = Math.sqrt(Math.pow(endMidX - pieCenterX, 2) + Math.pow(endMidY - pieCenterY, 2));

        let normalX = (endMidX - pieCenterX) / magnitude;
        let normalY = (endMidY - pieCenterY) / magnitude;

        if (Number(section.dataset.percent) > 0.5) {
            normalX = -normalX;
            normalY = -normalY;
        }

        section.style.cx = pieCenterX + (normalX * hoverDistance);
        section.style.cy = pieCenterY + (normalY * hoverDistance);

        // Section shadow
        let sectionShadow = document.getElementById(section.id + '-shadow');

        sectionShadow.style.cx = pieCenterX + (normalX * hoverDistance);
        sectionShadow.style.cy = pieCenterY + (normalY * hoverDistance);
    }

    // Fill in tooltip
    FillTooltip(section, title, prefix, value, valueType);

    // Show tooltip
    MoveTooltip(event, section);
}

// Updating a section of a pie chart
async function Pie_UpdateSection(section) {
    let pie = section.parentElement;
    let sectionRarity = section.dataset.rarity;

    let pieWidth = pie.clientWidth;
    let pieHeight = pie.clientHeight;
    let pieCenterX = pieWidth / 2;
    let pieCenterY = pieHeight / 2;

    let endMidX = section.dataset.endMidX;
    let endMidY = section.dataset.endMidY;

    let magnitude = Math.sqrt(Math.pow(endMidX - pieCenterX, 2) + Math.pow(endMidY - pieCenterY, 2));

    let normalX = (endMidX - pieCenterX) / magnitude;
    let normalY = (endMidY - pieCenterY) / magnitude;
    if (Number(section.dataset.percent) > 0.5) {
        normalX = -normalX;
        normalY = -normalY;
    }

    // Section shadow
    let sectionShadow = document.getElementById(section.id + '-shadow');

    if (section.dataset.selected == 'true') {
        section.style.cx = pieCenterX + (normalX * selectedDistance);
        section.style.cy = pieCenterY + (normalY * selectedDistance);

        sectionShadow.style.cx = pieCenterX + (normalX * selectedDistance);
        sectionShadow.style.cy = pieCenterY + (normalY * selectedDistance);

        sectionShadow.style.fill = 'rgba(255, 255, 255, 0.8)';
    }
    else if (section.matches(':hover')) {
        section.style.cx = pieCenterX + (normalX * hoverDistance);
        section.style.cy = pieCenterY + (normalY * hoverDistance);

        sectionShadow.style.cx = pieCenterX + (normalX * hoverDistance);
        sectionShadow.style.cy = pieCenterY + (normalY * hoverDistance);

        sectionShadow.style.fill = 'rgba(0, 0, 0, 0.5)';
    }
    else {
        section.style.cx = pieCenterX;
        section.style.cy = pieCenterY;

        sectionShadow.style.cx = pieCenterX;
        sectionShadow.style.cy = pieCenterY;

        sectionShadow.style.fill = 'rgba(0, 0, 0, 0.5)';
    }
}

// Resetting a section of a pie chart
function Pie_ResetSection(event, section) {
    if (section.dataset.selected != 'true') {
        let pie = section.parentElement;

        let pieWidth = pie.clientWidth;
        let pieHeight = pie.clientHeight;
        let pieCenterX = pieWidth / 2;
        let pieCenterY = pieHeight / 2;

        section.style.cx = pieCenterX;
        section.style.cy = pieCenterY;

        // Section shadow
        let sectionShadow = document.getElementById(section.id + '-shadow');

        sectionShadow.style.cx = pieCenterX;
        sectionShadow.style.cy = pieCenterY;

        sectionShadow.style.fill = 'rgba(0, 0, 0, 0.5)';
    }
}

//// Bar charts
function Bar_HoverSection(event, section, title, value) {
    let chart = section.parentElement;

    // Fill in tooltip
    FillTooltip(section, title, value);

    // Show tooltip
    MoveTooltip(event, section);
}

//// Tooltip for charts
var tooltipElement = document.getElementById('chart-tooltip');
var tooltipTitle = tooltipElement.getElementsByTagName('h1')[0];
var tooltipValue = tooltipElement.getElementsByTagName('p')[0];

// Tooltip when hovering a section of a chart
function FillTooltip(section, title, value) {
    let chart = section.parentElement;
    if (chart.classList.contains('bars'))
        chart = chart.parentElement;

    tooltipTitle.innerHTML = title;
    tooltipValue.innerHTML = chart.dataset.valuePrefix + ": " + value + " " + chart.dataset.valueType;
}

// Moving the tooltip of a chart
function MoveTooltip(event, section) {
    let chart = section.parentElement;
    if (chart.classList.contains('bars'))
        chart = chart.parentElement;

    tooltipElement.classList.remove('hidden');

    tooltipElement.style.top = event.clientY + 'px';
    tooltipElement.style.left = (event.clientX + 30) + 'px';

    let sectionRarity = section.dataset.rarity;
    if (sectionRarity)
        tooltipElement.classList.add(sectionRarity)

}

// Hiding the tooltip of a chart
function HideTooltip(event, section) {
    let chart = section.parentElement;
    if (chart.classList.contains('bars'))
        chart = chart.parentElement;

    tooltipElement.classList.add('hidden');

    let sectionRarity = section.dataset.rarity;
    if (sectionRarity)
        tooltipElement.classList.remove(sectionRarity)
}

