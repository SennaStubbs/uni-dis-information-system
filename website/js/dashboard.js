// Filters
var filterButtons = document.getElementById('filters').getElementsByTagName('button');

// Pie Charts
var hoverDistance = 5; // Pixels
var selectedDistance = 10; // Pixels

// Updating all charts when a new filter is applied
async function FilterUpdate(initialising) {
    for (let pieChart of document.getElementsByClassName('pie-chart')) {
        let rarityIndex = 0;
        for (let rarity of rarityOrder) {
            let section = document.getElementById(pieChart.id + '-' + rarity);
            if (section) {
                section.dataset.selected = selectedRarities.includes(rarity);
                if (selectedRarities.includes(rarity))
                    filterButtons[rarityIndex].classList.remove('not-selected')
                else
                    filterButtons[rarityIndex].classList.add('not-selected')

                
                Pie_UpdateSection(section);
            }
                
            rarityIndex++;
        }

        if (!pieChart.classList.contains('shadow')) {
            // Update total count
            let formData = new FormData();
            formData.append('rarities', selectedRarities);

            await fetch(window.location.origin + "/information_system/website/operations/dashboard/fetch_count", {
                method: 'POST',
                body: formData
            })
            .then((response) => response.text())
            .then((data) => {
                let totalElement = document.getElementById(pieChart.id + '-total');
                totalElement.innerHTML = data + " Items";
            });
        }
    }

    document.cookie = 'dashboard_rarities=' + selectedRarities.toString();
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
function Pie_HoverSection(event, section, title, value, valueType) {
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
    Pie_FillTooltip(section, title, value, valueType);

    // Show tooltip
    Pie_MoveTooltip(event, section);
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

// Tooltip when hovering a section of a pie chart
function Pie_FillTooltip(section, title, value, valueType) {
    let pie = section.parentElement;

    let tooltip = document.getElementById(pie.id + '-tooltip');

    let tooltipTitle = tooltip.getElementsByTagName('h1')[0];
    tooltipTitle.innerHTML = title;
    let tooltipValue = tooltip.getElementsByTagName('p')[0];
    tooltipValue.innerHTML = "Total: " + value + " " + valueType;
}

// Moving the tooltip of a pie chart
function Pie_MoveTooltip(event, section) {
    let pie = section.parentElement;
    let piePosition = pie.getBoundingClientRect();

    let tooltip = document.getElementById(pie.id + '-tooltip');
    tooltip.classList.remove('hidden');

    tooltip.style.top = (piePosition.top + event.offsetY) + 'px';
    tooltip.style.left = ((piePosition.left + event.offsetX) + 30) + 'px';

    let sectionRarity = section.dataset.rarity;
    if (sectionRarity)
        tooltip.classList.add(sectionRarity)

}

// Hiding the tooltip of a pie chart
function Pie_HideTooltip(event, section) {
    let pie = section.parentElement;

    let tooltip = document.getElementById(pie.id + '-tooltip');
    tooltip.classList.add('hidden');

    let sectionRarity = section.dataset.rarity;
    if (sectionRarity)
        tooltip.classList.remove(sectionRarity)
}

