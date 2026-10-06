\# CodeDiliver AI



\## AI-Powered Developer Assistant and Code Analysis Platform



CodeDiliver AI is a PHP and MySQL based developer assistant that helps developers upload their projects, analyze source code, identify common issues, and get AI-powered assistance for debugging and code improvement.



The platform combines automated code analysis with Google's Gemini AI to provide developers with useful project insights and an interactive coding assistant.



\## Features



\* Upload PHP projects as ZIP files

\* Automatic project structure scanning

\* Code quality scoring

\* Bug detection

\* Security issue checking

\* Performance issue checking

\* AI-generated project summary

\* Project-specific AI chat

\* Analysis reports and metrics

\* User authentication and project management

\* Support for PHP, JavaScript, HTML, CSS, SQL, Python, TypeScript, JSON and TXT files



\## AI Assistant



CodeDiliver AI uses \*\*Google Gemini 2.5 Flash\*\* to provide project-aware development assistance.



Developers can use the AI assistant for:



\* Code explanation

\* Debugging help

\* Refactoring suggestions

\* Programming questions

\* Project-specific analysis



\## Analysis Modules



| Module              | Purpose                                |

| ------------------- | -------------------------------------- |

| Project Scanner     | Scans project files and structure      |

| Quality Score       | Calculates code quality score          |

| Bug Detector        | Detects common coding issues           |

| Security Checker    | Identifies potential security concerns |

| Performance Checker | Finds potentially inefficient code     |

| AI Summary          | Generates a project overview           |

| Analysis Report     | Displays combined analysis results     |



\## Technology Stack



\*\*Frontend\*\*



\* HTML5

\* CSS3

\* JavaScript

\* Fetch API



\*\*Backend\*\*



\* PHP

\* MySQLi

\* PHP Sessions

\* REST-style APIs



\*\*AI\*\*



\* Google Gemini API

\* Gemini 2.5 Flash



\*\*Tools\*\*



\* XAMPP

\* Apache

\* MySQL



\## How It Works



```text

Register / Login

&#x20;      |

&#x20;      v

Upload Project ZIP

&#x20;      |

&#x20;      v

Project Scanner

&#x20;      |

&#x20;      v

Code Analysis

&#x20;  /    |    |    \\

&#x20;Bugs Security Performance Quality

&#x20;      |

&#x20;      v

&#x20;Analysis Report

&#x20;      |

&#x20;      v

&#x20;   AI Chat

```



\## Project Structure



```text

Ai\_bot/

├── analyzer/

│   ├── ai\_summary.php

│   ├── analysis\_result.php

│   ├── bug\_detector.php

│   ├── performance\_checker.php

│   ├── project\_scanner.php

│   ├── quality\_score.php

│   └── security\_checker.php

│

├── api/

│   ├── analyze.php

│   ├── chat\_ai.php

│   ├── upload.php

│   └── schema.sql

│

├── includes/

│   ├── auth.php

│   ├── connection.php

│   └── functions.php

│

├── dashboard.php

├── chat.php

├── my\_projects.php

├── project\_details.php

├── upload\_project.php

├── login.php

├── register.php

└── index.php

```



\## Installation



\### Requirements



\* XAMPP

\* Apache

\* MySQL

\* PHP 7+ / PHP 8+



\### Setup



1\. Clone or download the repository.

2\. Place the project inside the XAMPP `htdocs` folder.

3\. Start Apache and MySQL from XAMPP.

4\. Create the required MySQL database.

5\. Import `api/schema.sql`.

6\. Configure the local database connection.

7\. Add your own Gemini API key to the local configuration.

8\. Open the application in your browser.



```text

http://localhost/projects/Ai\_bot/

```



\## Security



The public repository does not contain real API keys, production passwords, or private database dumps.



Local credentials and private files are excluded through `.gitignore`.



\## Learning Outcomes



This project helped me gain practical experience in:



\* PHP and MySQL development

\* Authentication and sessions

\* File upload and ZIP processing

\* Static code analysis

\* REST-style APIs

\* AI API integration

\* Database-driven dashboards

\* Git and GitHub security practices



\## Future Improvements



\* Advanced code analysis

\* Support for more programming languages

\* AI-powered code refactoring

\* SQL analysis

\* Architecture diagram generation

\* GitHub repository integration

\* Downloadable analysis reports



\## Author



\*\*Ganesh Munde\*\*



GitHub:

https://github.com/ganeshmunde-dev



\## Repository



https://github.com/ganeshmunde-dev/CodeDiliver-AI



\## License



This project is developed for educational and portfolio purposes.



