
const errors_el = document.getElementById('error');
const all_inputs = {
    name: {
        error_text: 'Please enter a valid name (letters and numbers only).'
    },
    price: {
        error_text: 'Please enter a valid Price (numbers only).'
    },
    quantity: {
        error_text: 'Please enter a valid Quantity (numbers only).'
    },
    image: {
        error_text: 'Please enter at least one image.'
    },
    description: {
        error_text: 'Please describe your product or use the ai.'
    }
}
function handle_errors(errs) {
    let str = "You have errors with the following fields: ";

    errs.map((er) => {
        er.el.html(all_inputs[er.text].error_text);
        str += er.text + ", ";
    });

    let error_el = document.createElement('div');
    error_el.classList.add('error');
    error_el.innerText = str;

    error_el.addEventListener('click', function () {
        errors_el.removeChild(error_el);
    });

    errors_el.appendChild(error_el);
}