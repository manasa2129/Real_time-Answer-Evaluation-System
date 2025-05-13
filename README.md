# Real-Time Answer Evaluation System

A web-based application that enables students to upload handwritten or typed answer scripts and receive instant feedback powered by AI. Designed for educators to evaluate responses efficiently with real-time analysis and scoring.

## 🔍 Overview

This system simplifies the traditional manual evaluation process by integrating AI to assess grammar, relevance, and content quality. Educators can upload, review, and provide feedback through a dedicated dashboard, while students receive scores and suggestions instantly.

## 🛠️ Features

- ✍️ Student upload portal for answer scripts (PDF/images/text)
- 🤖 AI-driven evaluation of grammar, relevance, and overall quality
- ⚡ Real-time feedback using AJAX and JSON for seamless updates
- 📊 Educator dashboard to manage uploads and track evaluations
- 🗃️ MySQL backend with structured data storage for users, uploads, and feedback

## 🧑‍💻 Tech Stack

- **Frontend:** HTML, CSS, JavaScript, AJAX
- **Backend:** PHP (or Node.js if applicable)
- **Database:** MySQL
- **AI Model:** Python (using NLP libraries like NLTK or SpaCy)
- **Data Format:** JSON (for API communication)

## 🧱 Database Schema (Key Tables)

- `users` – Stores student and educator login info
- `uploads` – Records uploaded answer scripts
- `extracted_text` – Stores extracted content from documents
- `evaluations` – Contains scores and feedback results
- `feedback` – Stores AI and educator-generated suggestions

## 🚀 How It Works

1. **Educator uploads** their answer script.
2. **AI model processes** the script and generates scores for grammar, relevance, and overall quality.
3. **Evaluation results** are displayed instantly via AJAX-based updates.
4. **Educators can review** and override feedback through the dashboard.
5. Final feedback is stored and accessible to both parties.

## 📌 Future Improvements

- Role-based login authentication
- PDF and handwriting recognition (OCR)
- Detailed topic-wise feedback
- Educator performance analytics

## 📄 License

This project is for educational and research purposes.

---
