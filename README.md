# 🎬 Filmyheaven - Your Ultimate Movie Discovery Platform

<div align="center">

![Filmyheaven Banner](https://via.placeholder.com/1200x300/124E66/FFFFFF?text=Filmyheaven+-+Discover+Your+Next+Favorite+Movie)

**A Dynamic Movie Platform with AI-Powered Recommendations**

[![GitHub Repo](https://img.shields.io/badge/GitHub-botuserout%2FFilmyheaven-124E66?style=for-the-badge&logo=github)](https://github.com/botuserout/Filmyheaven)
[![Made with Love](https://img.shields.io/badge/Made%20with-❤️-red?style=for-the-badge)](https://github.com/botuserout/Filmyheaven)
[![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)

[Features](#-features) • [Installation](#-installation) • [Usage](#-usage) • [Screenshots](#-screenshots) • [Team](#-team)

</div>

---

## 📖 About The Project

**Filmyheaven** is a feature-rich, dynamic movie discovery platform designed to help users explore, manage, and enjoy their favorite films. With an intelligent AI chatbot named **MovieMoodBot**, personalized user accounts, and powerful administrative tools, Filmyheaven transforms the way you discover movies!

Whether you're looking for a movie based on your current mood, want to maintain a watchlist, or explore top-rated films, Filmyheaven has got you covered.

---

## ✨ Features

### 🎯 Core Features
- **🔍 Movie Discovery** - Browse through an extensive collection of movies with detailed information
- **🤖 AI MovieMoodBot** - Smart chatbot that recommends movies based on your mood, genre preferences, or keywords
- **👤 User Accounts** - Secure registration and login system for personalized experiences
- **📝 Watchlist Management** - Add movies to your personal watchlist and track what you want to watch
- **⭐ Rating & Reviews** - Rate movies and write reviews to share your opinions
- **🎭 Genre Filtering** - Filter movies by genre, mood, release year, and more
- **📊 Personalized Recommendations** - Get movie suggestions tailored to your viewing history

### 🛠️ Admin Features
- **🎬 Movie Management** - Add, edit, or remove movies from the database
- **👥 User Management** - Monitor and manage user accounts
- **📈 Analytics Dashboard** - Track platform statistics and user engagement
- **🎨 Content Curation** - Curate featured movies and trending collections

### 🤖 MovieMoodBot Capabilities
- Friendly, conversational AI interface
- Mood-based recommendations (happy, sad, romantic, thrilling, etc.)
- Genre-specific suggestions (action, comedy, drama, horror, etc.)
- Real-time database queries for accurate results
- Natural language understanding

---

## 🚀 Tech Stack

| Technology | Purpose |
|------------|---------|
| **PHP** | Server-side scripting and business logic |
| **MySQL** | Database management system |
| **HTML5/CSS3** | Frontend structure and styling |
| **JavaScript** | Interactive features and AJAX calls |
| **XAMPP** | Local development environment |
| **JSON** | Data exchange format for API |

---

## 💻 Installation

Follow these steps to set up Filmyheaven on your local machine:

### Prerequisites
- XAMPP (or any PHP 7.4+ and MySQL server)
- Web browser (Chrome, Firefox, Edge, etc.)
- Text editor (VS Code, Sublime, etc.)

### Step-by-Step Setup

1. **Download and Install XAMPP**
   ```bash
   Download from: https://www.apachefriends.org/
   Install with Apache and MySQL modules
   ```

2. **Clone or Download the Repository**
   ```bash
   # Option 1: Clone via Git
   git clone https://github.com/botuserout/Filmyheaven.git
   
   # Option 2: Download ZIP from GitHub
   # Extract to your desired location
   ```

3. **Move Project to XAMPP Directory**
   - Copy the entire `filmyheaven` folder
   - Paste it into `C:\xampp\htdocs\` (Windows) or `/Applications/XAMPP/htdocs/` (Mac)
   - Final path should be: `C:\xampp\htdocs\filmyheaven\`

4. **Start XAMPP Services**
   - Open XAMPP Control Panel
   - Start **Apache** server
   - Start **MySQL** database
   - Ensure both show green "Running" status

5. **Create Database**
   - Open browser and go to: `http://localhost/phpmyadmin`
   - Click **"New"** to create a database
   - Database name: `filmyheaven_db` (or as configured in your code)
   - Collation: `utf8mb4_general_ci`
   - Click **"Create"**

6. **Import Database Schema**
   - Select your newly created database
   - Click **"Import"** tab
   - Choose file: `filmyheaven/database/filmyheaven.sql` (if provided)
   - Click **"Go"** to import tables and data
   - If no SQL file exists, create tables manually using the database structure

7. **Configure Database Connection**
   - Open `filmyheaven/api/db.php` in your text editor
   - Update the following credentials if needed:
   ```php
   $host = 'localhost';
   $dbname = 'filmyheaven_db';
   $username = 'root';
   $password = ''; // Leave empty for default XAMPP
   ```

8. **Launch the Application**
   - Open your browser
   - Navigate to: `http://localhost/filmyheaven/`
   - You should see the Filmyheaven homepage!

---

## 🎮 Usage

### For Users

1. **Registration**
   - Click "Sign Up" or "Register"
   - Fill in your details (username, email, password)
   - Submit to create your account

2. **Login**
   - Enter your credentials
   - Access your personalized dashboard

3. **Discover Movies**
   - Browse the movie catalog
   - Use filters to find movies by genre, year, or rating
   - Click on any movie to view detailed information

4. **Use MovieMoodBot**
   - Click the 🎬 chat icon in the bottom-right corner
   - Tell the bot your mood (e.g., "I'm feeling sad" or "I want action movies")
   - Get instant personalized recommendations

5. **Manage Watchlist**
   - Add movies to your watchlist for later
   - Mark movies as watched
   - Track your viewing progress

6. **Rate & Review**
   - Rate movies from 1-5 stars
   - Write reviews to share your thoughts
   - Read reviews from other users

### For Administrators

1. **Access Admin Panel**
   - Login with admin credentials
   - Navigate to `/admin/` or admin dashboard

2. **Manage Movies**
   - Add new movies with details (title, genre, year, poster, description)
   - Edit existing movie information
   - Delete movies from the catalog

3. **User Management**
   - View all registered users
   - Manage user roles and permissions
   - Monitor user activity

---

## 📁 Project Structure

```
filmyheaven/
│
├── api/                    # Backend API files
│   ├── chatbot.php        # MovieMoodBot AI endpoint
│   ├── db.php             # Database connection
│   ├── login.php          # User authentication
│   ├── register.php       # User registration
│   └── mail_utils.php     # Email utilities
│
├── css/                    # Stylesheets
│   ├── admin.css          # Admin panel styles
│   └── style.css          # Main application styles
│
├── js/                     # JavaScript files
│   └── main.js            # Core frontend logic
│
├── images/                 # Image assets
│   └── posters/           # Movie poster images
│
├── admin/                  # Admin panel pages
│   ├── dashboard.php      # Admin dashboard
│   ├── manage_movies.php  # Movie management
│   └── manage_users.php   # User management
│
├── database/               # Database files
│   └── filmyheaven.sql    # Database schema
│
├── index.php               # Homepage
├── movie.php               # Movie details page
├── watchlist.php           # User watchlist
├── chatbot.html            # Chatbot widget
└── README.md               # This file
```

---

## 📸 Screenshots

> 🚧 **Coming Soon!** Screenshots of the platform will be added here.

| Homepage | MovieMoodBot | Movie Details |
|----------|--------------|---------------|
| ![Home](placeholder) | ![Chatbot](placeholder) | ![Details](placeholder) |

---

## 👥 Team

This project was created with dedication and passion by:

<table>
  <tr>
    <td align="center">
      <img src="https://via.placeholder.com/150/124E66/FFFFFF?text=R" width="100px;" style="border-radius:50%"/><br />
      <sub><b>Rakesh</b></sub><br />
      <sub>IU2341230372</sub><br />
      <sub>Full Stack Developer</sub>
    </td>
    <td align="center">
      <img src="https://via.placeholder.com/150/124E66/FFFFFF?text=RP" width="100px;" style="border-radius:50%"/><br />
      <sub><b>Rahil Patel</b></sub><br />
      <sub>IU2341230375</sub><br />
      <sub>Full Stack Developer</sub>
    </td>
  </tr>
</table>

---

## 🔗 Links

- **GitHub Repository**: [https://github.com/botuserout/Filmyheaven](https://github.com/botuserout/Filmyheaven)
- **Live Demo**: *Coming Soon*
- **Documentation**: *Coming Soon*

---

## 🤝 Contributing

Contributions, issues, and feature requests are welcome! Feel free to check the [issues page](https://github.com/botuserout/Filmyheaven/issues).

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📝 License

This project is open source and available for educational purposes.

---

## 🙏 Acknowledgments

- Movie data and information sourced from public APIs
- Icons and design inspiration from modern web design trends
- Special thanks to the open-source community

---

## 📞 Contact & Support

For questions, suggestions, or support:

- **Repository Issues**: [GitHub Issues](https://github.com/botuserout/Filmyheaven/issues)
- **Developers**: Rakesh (IU2341230372) & Rahil Patel (IU2341230375)

---

<div align="center">

### ⭐ Star this repo if you find it helpful!

**Made with ❤️ by Rakesh & Rahil Patel**

[Back to Top](#-filmyheaven---your-ultimate-movie-discovery-platform)

</div>

