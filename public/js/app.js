// DOM Elemente selektieren
const startBtn = document.getElementById('start-quiz-btn');
const nextBtn = document.getElementById('next-btn');
const startScreen = document.getElementById('start-screen');
const quizScreen = document.getElementById('quiz-screen');
const questionText = document.getElementById('question-text');
const answerButtons = document.getElementById('answer-buttons');

// Aktueller Fragen-Speicher (hilft uns beim Abgleich)
let currentQuestionData = null;

// Event Listener für den Start-Button
startBtn.addEventListener('click', () => {
    startScreen.classList.add('hidden');
    quizScreen.classList.remove('hidden');
    fetchQuestion();
});

// Event Listener für den Next-Button
nextBtn.addEventListener('click', () => {
    nextBtn.classList.add('hidden');
    fetchQuestion();
});

// Funktion, um eine Frage von der API zu holen
async function fetchQuestion() {
    try {
        const response = await fetch('api/get_question.php');
        const question = await response.json();
        
        currentQuestionData = question; // Wir merken uns die ganze Frage
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
        // Wichtig: Wir hängen die Event-Logik direkt an
        button.addEventListener('click', () => selectAnswer(button, answer));
        answerButtons.appendChild(button);
    });
}

// Die kombinierte Logik für die Antwort-Auswahl
function selectAnswer(clickedButton, selectedAnswer) {
    const allButtons = document.querySelectorAll('#answer-buttons button');
    
    allButtons.forEach(btn => {
        btn.disabled = true; // Klick sperren
        
        // Finde die richtige Antwort in den Daten, um sie IMMER grün zu markieren
        // Das ist der "Profi-Kniff" für bessere UX
        const relatedAnswerData = currentQuestionData.answers.find(a => a.text === btn.innerText);
        
        if (relatedAnswerData.isCorrect) {
            btn.classList.add('correct'); // Die Richtige wird immer grün
        }
        
        // Wenn der User diesen Button geklickt hat und er falsch war -> rot
        if (btn === clickedButton && !selectedAnswer.isCorrect) {
            btn.classList.add('wrong');
        }
    });

    nextBtn.classList.remove('hidden');
}

const loginSubmitBtn = document.getElementById('login-submit-btn');
const usernameInput = document.getElementById('login-username');
const passwordInput = document.getElementById('login-password');
const loginError = document.getElementById('login-error');
const loginScreen = document.getElementById('login-screen');

loginSubmitBtn.addEventListener('click', async () => {
    const username = usernameInput.value;
    const password = passwordInput.value;

    try {
        const response = await fetch('api/login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, password })
        });

        const result = await response.json();

        if (result.success) {
            loginScreen.classList.add('hidden');
            startScreen.classList.remove('hidden');
            alert('Willkommen, ' + result.username + '! Du bist jetzt eingeloggt.');
        } else {
            loginError.innerText = result.message;
            loginError.classList.remove('hidden');
        }
    } catch (error) {
        console.error('Login-Fehler:', error);
    }
});