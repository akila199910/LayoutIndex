$(document).ready(function () {
    document.querySelectorAll('.tom_select').forEach((selectElement) => {
        new SlimSelect({
            select: selectElement
        })
    });
});

function ajax_slim_select(selectElement,data) {
    // Assuming slimSelect is stored in a variable when initialized
    console.log(selectElement);

    const slimSelectInstance = new SlimSelect({
        select: `${selectElement}`
    });

    // Update the select options
    slimSelectInstance.setData(data);
}
