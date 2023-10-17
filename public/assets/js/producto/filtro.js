let $selectPublished, $selectCategory;

$(function (){
    $selectPublished = $('#select-published');
    $selectCategory = $('#select-category');

    $('.switch').on('click', onClickSwitchPublished);

    $selectPublished.change('onChangeFilter');
    $selectCategory.change('onChangeFilter');
});

function onClickSwitchPublished(){
    const id = $(this).data('id');
    const value = $(this).data('to');
    location.href = "/producto/filtro/${id}/turn?published=${value}";
}

function onChangeFilter() {
    const published = $selectPublished.val();
    const categoryId = $selectCategory.val();
    location.href = "/producto/filtro/${id}/turn?published=${published}&category_id=${categoryId}";
}