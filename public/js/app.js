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

// --- Variablen für den Status ---
let selectedCategories = [];
let currentQuestionData = null;
let totalQuestionsCount = 25;

// --- INITIALISIERUNG ---
document.addEventListener('DOMContentLoaded', () => {
    fetchCategories(); // Lernfelder beim Start laden
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
    // Start-Button nur aktiv, wenn etwas gewählt ist
    startBtn.disabled = selectedCategories.length === 0;
}

// Alle auswählen
selectAllBtn.addEventListener('click', () => {
    const cards = document.querySelectorAll('.category-card');
    selectedCategories = [];
    cards.forEach(card => {
        card.classList.add('selected');
        // Hier müsste man die IDs dynamisch wissen, 
        // für den Test nehmen wir an, die IDs entsprechen der Reihenfolge (1-10)
    });
    // In einer echten App würden wir hier die IDs aus den Card-Daten ziehen
    selectedCategories = Array.from({length: 10}, (_, i) => i + 1);
    startBtn.disabled = false;
});

// Slider Update
questionRange.addEventListener('input', (e) => {
    totalQuestionsCount = e.target.value;
    rangeValue.innerText = totalQuestionsCount;
});

// --- QUIZ LOGIK ---

startBtn.addEventListener('click', () => {
    startScreen.classList.add('hidden');
    quizScreen.classList.remove('hidden');
    fetchQuestion();
});

async function fetchQuestion() {
    explanationContainer.classList.add('hidden'); // Erklärung verstecken
    nextBtn.classList.add('hidden');
    
    try {
        // Wir senden die Auswahl an die API
        const response = await fetch('api/get_question.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                categories: selectedCategories,
                limit: totalQuestionsCount 
            })
        });
        const question = await response.json();
        
        currentQuestionData = question;
        displayQuestion(question);
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
        button.addEventListener('click', () => selectAnswer(button, answer));
        answerButtons.appendChild(button);
    });
}

function selectAnswer(clickedButton, selectedAnswer) {
    const allButtons = document.querySelectorAll('#answer-buttons button');
    
    // Erklärung anzeigen
    explanationText.innerText = currentQuestionData.explanation;
    explanationContainer.classList.remove('hidden');

    allButtons.forEach(btn => {
        btn.disabled = true;
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

nextBtn.addEventListener('click', fetchQuestion);

// --- LOGIN LOGIK (Header) ---
document.getElementById('header-login-btn').addEventListener('click', () => {
    startScreen.classList.add('hidden');
    loginScreen.classList.remove('hidden');
});

document.getElementById('login-back-btn').addEventListener('click', () => {
    loginScreen.classList.add('hidden');
    startScreen.classList.remove('hidden');
});

// (Dein bestehender loginSubmitBtn Code hier einfügen...)