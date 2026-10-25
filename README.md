# Budapest Project Voting 🗳️

A community budget voting site written in plain PHP. Residents can submit ideas for local projects in Budapest and vote on the ones they want to see happen. It's built without frameworks or a database: all data is stored in JSON files.

## Features

### For everyone
- Browse approved projects, **grouped by category**, with vote counts
- Filter projects by category using a dropdown
- Open any project to see its details: description, image, postal code, creator, and submission and approval dates

### For registered users
- **Sign up, log in, log out.** Passwords are stored hashed with `password_hash`.
- **Submit new projects.** The ID, owner and submission date are filled in automatically.
- **Vote on projects**, with these rules:
  - one vote per project
  - at most **3 votes per category**, and the page shows how many you have left
  - votes can be taken back
  - voting is only open for **2 weeks after a project is approved**. After that, the project is marked "Voting Closed".

### Form validation
- **Sign-up:** usernames must be unique and contain no spaces. The email must be valid. The password needs at least 8 characters with lowercase and uppercase letters and a digit, and both password fields must match.
- **New project:** the title needs at least 10 characters and the description at least 150. The category comes from a fixed list. The postal code must be a **valid Budapest postal code** (`1` + district `01`–`23` + `1`–`9`, or `1007`). The image link is optional but must be a valid URL if given.

### Categories
Small scale local project · Big scale local project · Opportunity creator · Green Budapest

## Running it

You need PHP 8 or newer. In the project folder, run:

```
php -S localhost:8000
```

Then open http://localhost:8000.

There's a built-in admin account for testing. Log in with username `admin` and password `admin`.

## Project structure

```
├── index.php            # main page: project list, filtering, voting
├── project.php          # project details
├── addproject.php       # submit a new project
├── signup.php / login.php / logout.php
├── auth.php             # authentication + roles
├── storage.php          # generic JSON file storage (IStorage interface)
├── userstorage.php / projectstorage.php / votestorage.php
├── utils.php            # helpers (redirect)
├── style.css
└── *.json               # data: users, projects, votes, categories
```

## About

Made for the Web Programming course at ELTE (2025), as the PHP assignment.
