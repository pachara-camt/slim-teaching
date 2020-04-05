# slim-teaching

This repository for course 954240.
In the chapters of slim framework.

## Instrallation

1.  Install [git](https://git-scm.com/)
2.  In command-line change directory to web server document root with your student ID, e.g. `C:\xampp\htdocs\612110xxx`.
3.  Clone this repository.

    ```
    git clone https://github.com/pachara-camt/slim-teaching.git slim
    ```

    If you already have `slim` directory, try to remove or rename the existing one.
4.  Change directory to `slim`.
5.  Install all required libraries.

    ```
    composer install
    ```

6.  Create your own `.env` file.

## Snapshots

Instead of using git command, you can use the following snapshots.
Extract the zip file to web server document root with your student ID, e.g. `C:\xampp\htdocs\612110xxx`, and rename directory to `slim`.
Then start from step 4. in instrallation section.
Select one that you can work with.

[After finish part 1 assignments](../../archive/part-1-finish.zip).

[Before start part 2 assignments](../../archive/part-2-pre.zip).

[After finish part 2 assignments](../../archive/part-2-final.zip).

[Advanced template approach that uses the the following pattern](../../archive/advanced-template.zip).

    1. Use the following routes names convention.
        - list action suffix with `-list`.
        - view action suffix with `-view`.
        - add form action suffix with `-add-form`.
        - add to database action suffix with `-add`.
        - update form action suffix with `-update-form`.
        - update to database action suffix with `-update`.
        - delete from database action suffix with `-delete`.
    2. All list pages inherite from `list-layout.html` tempalte.
    3. All view pages inherite from `view-layout.html` tempalte.
    4. All form pages inherite from `form-layout.html` tempalte.
    5. Reuse the same form tempalte for both add and update action.
    6. Organize template by using directory name instead of name prefix.
    7. Separate flash message template segment to individual file that can be used in many templates.

[More advanced configuraion](../../archive/advanced-configuration.zip).

    1. Each configuration files return closure function that make many IDE work with auto-complete.
    2. Use more [material icons](https://google.github.io/material-design-icons/).
