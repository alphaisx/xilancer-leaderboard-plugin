### Leaderboard Setup
*Make sure is setup is done before moving to the next one*
1. After adding plugin to the system (make sure migration is successful run and validate if plugin is working) 
2. Remember to publish asset files ```php artisan vendor:publish --tag=leaderboard-assets```
3. Create a new leaderboard page in admin dashboard by going to
```https://gigafro.com/admin/dynamic-pages/add-new```
Leave the content empty and make sure the default slug is at (```/leaderboard```), it would be automatically filled by plugin itself.

4. After that navigate to ```https://gigafro.com/admin/plugins/menu/all``` and edit the default menu, add the new leaderboard page to the menu.

**That Ends the Leaderboard Setup**
