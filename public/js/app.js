// DOM Elemente selektieren
const startBtn = document.getElementById('start-quiz-btn');
const startScreen = document.getElementById('start-screen');
const quizScreen = document.getElementById('quiz-screen');
const questionText = document.getElementById('question-text');
const answerButtons = document.getElementById('answer-buttons');

// Event Listener für den Start-Button
startBtn.addEventListener('click', () => {
    startScreen.classList.add('hidden');
    quizScreen.classList.remove('hidden');
    fetchQuestion();
});

// Funktion, um eine Frage von der API zu holen
async function fetchQuestion() {
    try {
        const response = await fetch('api/get_question.php');
        const question = await response.json();
        
        displayQuestion(question);
    } catch (error) {
        console.error('Fehler beim Laden der Frage:', error);
    }
}

// Funktion, um die Frage im HTML anzuzeigen
function displayQuestion(question) {
    questionText.innerText = question.text;
    answerButtons.innerHTML = ''; // Vorherige Antworten löschen

    question.answers.forEach(answer => {
        const button = document.createElement('button');
        button.innerText = answer.text;
        button.classList.add('btn');
        button.addEventListener('click', () => selectAnswer(answer));
        answerButtons.appendChild(button);
    });
}

function selectAnswer(answer) {
    if (answer.isCorrect) {
        alert('Richtig! Gut gemacht.');
    } else {
        alert('Leider falsch. Versuch es weiter!');
    }
    // Hier könnten wir später fetchQuestion() erneut aufrufen für die nächste Frage
}