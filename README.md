# COMaster2

## Info

**Content management system for journal publishing**

This is the 2nd edition of the CMS, designed to simplify editorial processes and improve user experience.

The platform is an end-to-end software system that assists at every stage of the peer-reviewed publication process. It allows organizing and monitoring the editorial workflow of an academic journal from paper submission to publication and is intended for members of the editorial board. 

COMaster2 includes various tools, such as a submission wizard, an expert database, a review monitor, email notifications, an official document generator and many others, aimed at automating routine tasks, increasing the efficiency of editorial processes and reducing the cost of journal publishing.

The platform was deployed on the infrastructure of Image Processing Systems Institute of the Russian Academy of Sciences, implemented into the management process of Computer Optics journal and supported from 2018 to 2021.

## Table of Contents
- [Features](#features)
  - [Common](#common)
  - [Functionality](#functionality)
- [Installation](#installation)
  - [For development](#for-development)
  - [For production](#for-production)
  - [For backup](#for-backup)
- [License](#license)

## Features

### Common
- PHP v7.4
- WordPress v5.8.3
- Bootstrap v3.3.7
- jQuery v3.3.1
- MySQL v8
- Dockerized

### Functionality
- Submission wizard with automatic parsing of paper metadata
- Organizing peer-review process
- Email notifications of all changes to authors and section editors
- Automatic generation of review forms (in English and Russian)
- Tracking the current status of papers
- Submission statistics
- Highlighting papers that require special attention (e.g. delays, pending acceptance/rejection, etc.)
- PDF-preview
- Formation of journal issues
- Expert database
- Statistics of experts and personal rate
- Supportive information for editorial board meetings
- CRUD for Papers, Experts and Reviews
- Quick search
- Editorial chat
- Log journal
- Automatic reminder for reviewers and authors
- Integration with The BAT mailer
- Role-base access:
  - Chief editor: general information
  - Secretary: all functionality
  - Section editor: section-specific information, scientific acceptance
  - Technical editor: issue formation, technical acceptance
  - Layout designer: technical comments

## Installation

### For development

1. Create `.env`

2. Create `wp-config.php`

3. Fill in the salt in `wp-config.php`
```sh
curl https://api.wordpress.org/secret-key/1.1/salt/
```

4. Deploy
```sh
./deploy.sh
```

5. Create admin user
```sh
docker exec -it dev_com2_server php init.php
```

### For production

1. Create `.env`

2. Create `wp-config.php`

3. Fill in the salt in `wp-config.php`
```sh
curl https://api.wordpress.org/secret-key/1.1/salt/
```

4. Change ownership
```sh
sudo chown www-data:www-data .
sudo chown www-data:www-data ./files
sudo chmod 775 .
```

5. Deploy
```sh
./deploy.sh --prod
```

6. Create admin user
```sh
docker exec -it prod_com2_server php init.php
```

7. Nginx proxy  
Don't forget to set Host in global nginx settings
```sh
proxy_set_header Host $http_host;
```

### For backup

1. Install pv
```sh
sudo apt update && sudo apt install pv
```

## License

MIT License