window.onload = function() {
    document.getElementById('controllForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Отменяем стандартную отправку формы

        const formData = new FormData(this); // Создаем FormData из формы

        // Добавляем данные кнопки отправки в FormData
        const submitButton = event.submitter; // Получаем элемент, вызвавший отправку
        formData.append(submitButton.name, submitButton.value);

        document.getElementById('lsd').classList.add('text-bg-warning');
        document.getElementById('lsd').classList.remove('text-bg-success');
        document.getElementById('lsd').classList.remove('text-bg-danger');
        fetch('/', { // Замените на ваш URL
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            document.getElementById('lsd').classList.remove('text-bg-warning');
            document.getElementById('lsd').classList.add('text-bg-success');
            document.getElementById('lsd').innerHTML = data; // Обработка ответа
        })
        .catch(error => {
            document.getElementById('lsd').classList.remove('text-bg-warning');
            document.getElementById('lsd').classList.add('text-bg-danger');
            document.getElementById('lsd').innerHTML = data; // Обработка ответа
            console.error('Ошибка:', error);
        });
    });
};