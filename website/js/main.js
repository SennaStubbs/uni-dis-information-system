// Prevent resubmission page
if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
}

//// Popup
let popupTemplate =
`<div class="confirmation-popup" id="popup">
    <div class="popup-box frutiger-glossy">
        <p class="question">Are you sure?</p>
        <p class="message">This will delete item ID 0 (item name) permanently.</p>
        <div class="buttons">
            <button class="frutiger-tile continue">Continue</button>
            <button class="frutiger-tile cancel" onclick="ClosePopup()">Cancel</button>
        </div>
    </div>
</div>`;


var popupElement = document.getElementById('popup');
if (popupElement) {
    var popupTitle = document.getElementById('popup').getElementsByClassName('question')[0];
    var popupMessage = document.getElementById('popup').getElementsByClassName('message')[0];
    var popupContinueButton = document.getElementById('popup').getElementsByClassName('continue')[0];
    var popupCancelButton = document.getElementById('popup').getElementsByClassName('cancel')[0];
}

var popup_ContinueFunction;

function ConfirmationPopup(question, message, action, actionValues) {
    popupElement.classList.remove('hidden');

    popupTitle.innerHTML = question;
    popupMessage.innerHTML = message;

    popupCancelButton.style.display = "block";

    if (action == 'delete_item' && actionValues['itemId']) {
        function _() {
            Post_DeleteItem(actionValues['itemId']);
        }
        popupContinueButton.addEventListener('click', _);
        popup_ContinueFunction = _;
    }
    else if (action == 'edit_item' && actionValues['itemId']) {
        function _() {
            Post_EditItem(actionValues['itemId']);
        }
        popupContinueButton.addEventListener('click', _);
        popup_ContinueFunction = _;
    }
    else if (action == 'cancel_edit' && actionValues['itemId']) {
        function _() {
            CancelItemEdit(actionValues['itemId']);
            ClosePopup();
            currentlyEditingId = -1;
            defaultEditingValues = {};

            // If trying to edit an item while another item is currently being edited
            if (actionValues['tryingToEdit_ItemId']) {
                EditItem(actionValues['tryingToEdit_ItemId']);
            }
        }
        popupContinueButton.addEventListener('click', _);
        popup_ContinueFunction = _;
    }
        
}

function MessagePopup(title, message) {
    popupElement.classList.remove('hidden');

    popupTitle.innerHTML = title;
    popupMessage.innerHTML = message;

    popupCancelButton.style.display = "none";

    if (popup_ContinueFunction != null) {
        popupContinueButton.removeEventListener('click', popup_ContinueFunction);
        popup_ContinueFunction = null;
    }

    
    popupContinueButton.addEventListener('click', ClosePopup);
    popup_ContinueFunction = ClosePopup;
}

function ClosePopup() {
    document.getElementById('popup').classList.add('hidden');

    if (popup_ContinueFunction != null) {
        popupContinueButton.removeEventListener('click', popup_ContinueFunction);
        popup_ContinueFunction = null;
    }
}


//// Page styles
var currentPageStyle = localStorage['page-style'] || "green"; // Initial value is 'green' if no value is cached
function UpdatePageStyle() {
    document.body.classList = currentPageStyle;
    document.getElementById('theme').value = currentPageStyle;
}
UpdatePageStyle()

//// Input listener
document.addEventListener('input', function(event)
{
    // Page style changed
    if (event.target.id == 'theme')
    {
        currentPageStyle = event.target.value

        // Cache selected page style
        localStorage['page-style'] = currentPageStyle;

        // Update page with new page style
        UpdatePageStyle()
    }
    else return;
});