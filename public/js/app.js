// --- DOM Elemente ---
const startBtn = document.getElementById('start-quiz-btn');
const nextBtn = document.getElementById('next-btn');
const startScreen = document.getElementById('start-screen');
const quizScreen = document.getElementById('quiz-screen');
const loginScreen = document.getElementById('login-screen');
const categoryGrid = document.getElementById('category-grid');
const questionRange = document.getElementById('question-range');
const rangeValue = document.getElementById('range-value');
const selectAllBtn = document.getElementById('select-all-btn');
const explanationContainer = document.getElementById('explanation-container');
const explanationText = document.getElementById('explanation-text');
const counterElement = document.getElementById('question-counter'); // Element für 1 / X

// --- Variablen für den Status ---
let selectedCategories = [];
let currentQuestionData = null;
let totalQuestionsCount = 25; // Standardwert vom Slider
let currentQuestionIndex = 0; // Zähler für den Fortschritt

// --- INITIALISIERUNG ---
document.addEventListener('DOMContentLoaded', () => {
    fetchCategories(); 
    // Initialwert für Slider setzen
    totalQuestionsCount = questionRange.value;
});

// --- KATEGORIEN & DASHBOARD ---

async function fetchCategories() {
    try {
        const response = await fetch('api/get_categories.php');
        const categories = await response.json();
        renderCategoryCards(categories);
    } catch (error) {
        console.error('Fehler beim Laden der Kategorien:', error);
        categoryGrid.innerHTML = '<p class="error-text">Lernfelder konnten nicht geladen werden.</p>';
    }
}

function renderCategoryCards(categories) {
    categoryGrid.innerHTML = '';
    categories.forEach(cat => {
        const card = document.createElement('div');
        card.className = 'category-card';
        card.dataset.id = cat.id; // ID in der Karte speichern
        card.innerHTML = `
            <h4>${cat.short_name}</h4>
            <p>${cat.full_name}</p>
        `;
        card.addEventListener('click', () => toggleCategory(card, cat.id));
        categoryGrid.appendChild(card);
    });
}

function toggleCategory(card, categoryId) {
    if (selectedCategories.includes(categoryId)) {
        selectedCategories = selectedCategories.filter(id => id !== categoryId);
        card.classList.remove('selected');
    } else {
        selectedCategories.push(categoryId);
        card.classList.add('selected');
    }
    startBtn.disabled = selectedCategories.length === 0;
}

selectAllBtn.addEventListener('click', () => {
    const cards = document.querySelectorAll('.category-card');
    selectedCategories = [];
    cards.forEach(card => {
        card.classList.add('selected');
        selectedCategories.push(parseInt(card.dataset.id));
    });
    startBtn.disabled = false;
});

questionRange.addEventListener('input', (e) => {
    totalQuestionsCount = parseInt(e.target.value);
    rangeValue.innerText = totalQuestionsCount;
});

// --- QUIZ LOGIK ---

startBtn.addEventListener('click', () => {
    currentQuestionIndex = 0; // Reset bei Neustart
    startScreen.classList.add('hidden');
    quizScreen.classList.remove('hidden');
    
    // UI initialisieren
    updateCounterDisplay();
    fetchQuestion();
});

function updateCounterDisplay() {
    if (counterElement) {
        counterElement.innerText = `Frage ${currentQuestionIndex + 1} / ${totalQuestionsCount}`;
    }
}

async function fetchQuestion() {
    explanationContainer.classList.add('hidden');
    nextBtn.classList.add('hidden');

    try {
        const response = await fetch('api/get_question.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                categories: selectedCategories,
                limit: totalQuestionsCount
            })
        });
        const question = await response.json();

        if (question.error) {
            alert("Keine Fragen gefunden!");
            return;
        }

        currentQuestionData = question;
        displayQuestion(question);
        updateCounterDisplay(); // Counter bei jeder neuen Frage updaten
    } catch (error) {
        console.error('Fehler beim Laden der Frage:', error);
    }
}

function displayQuestion(question) {
    document.getElementById('question-text').innerText = question.text;
    document.getElementById('category-badge').innerText = question.category_name;
    const answerButtons = document.getElementById('answer-buttons');
    answerButtons.innerHTML = '';

    question.answers.forEach(answer => {
        const button = document.createElement('button');
        button.innerText = answer.text;
        button.className = 'answer-btn'; // Wichtig für dein Wer-Wird-Millionär CSS
        button.addEventListener('click', () => selectAnswer(button, answer));
        answerButtons.appendChild(button);
    });
}

function selectAnswer(clickedButton, selectedAnswer) {
    const allButtons = document.querySelectorAll('#answer-buttons button');

    explanationText.innerText = currentQuestionData.explanation;
    explanationContainer.classList.remove('hidden');

    allButtons.forEach(btn => {
        btn.disabled = true;
        // Die passende Antwort aus den Daten finden
        const answerData = currentQuestionData.answers.find(a => a.text === btn.innerText);

        if (answerData.is_correct) {
            btn.classList.add('correct');
        }
        if (btn === clickedButton && !selectedAnswer.is_correct) {
            btn.classList.add('wrong');
        }
    });

    nextBtn.classList.remove('hidden');
}

nextBtn.addEventListener('click', () => {
    currentQuestionIndex++;
    
    if (currentQuestionIndex < totalQuestionsCount) {
        fetchQuestion();
    } else {
        // Quiz Ende
        quizScreen.innerHTML = `
            <div style="text-align:center; padding: 50px;">
                <h2>Glückwunsch!</h2>
                <p>Du hast alle ${totalQuestionsCount} Fragen beantwortet.</p>
                <button onclick="location.reload()" class="answer-btn" style="margin-top:20px">Zurück zum Start</button>
            </div>
        `;
    }
});

// --- LOGIN LOGIK ---
document.getElementById('header-login-btn').addEventListener('click', () => {
    startScreen.classList.add('hidden');
    quizScreen.classList.add('hidden'); // Sicherstellen, dass alles andere weg ist
    loginScreen.classList.remove('hidden');
});

document.getElementById('login-back-btn').addEventListener('click', () => {
    loginScreen.classList.add('hidden');
    startScreen.classList.remove('hidden');
});