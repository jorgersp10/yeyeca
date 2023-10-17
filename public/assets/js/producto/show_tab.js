const targets  = document.querySelectorAll('[data-target]')
const contents = document.querySelectorAll('[data-content]')
//var token_form=tokens_form[0].value;

targets.forEach(target =>{
    target.addEventListener('click', () => {
        contents.forEach(c =>{
            c.classList.remove('active')
        })
        //alert("Click")
        const t = document.querySelector(target.dataset.target)
        t.classList.add('active')

            //$('#monto').mask('000,000,000,000.00', { reverse: true })

    })
})

