# PHP - Lumen - Popularin API
**EN**: API for Popularin - Indonesian film social media.

**ID**: API untuk Popularin - Sosial media film Indonesia.

## Requirements
1. Composer
2. Lumen
3. XAMPP

## How to Use
1. Clone this repository to your desired location.
2. Create database and configure it on `.env` file.
3. Run migration `php artisan migrate`.
4. Run seeder `php artisan db:seed`.
5. Run server `php -S localhost:8000 -t public`.
6. Fire-up Postman.
7. Do-what-you-want-with-it!

## Endpoints
### GET
- Get app version  
   `/api`  
- Get comments from a review  
   `/api/review/{review_id}/comments`  
- Get users who liked a comment from everyone  
   `/api/comment/{comment_id}/likes/from/all`  
- Get users who liked a comment from following  
   `/api/comment/{comment_id}/likes/from/following`  
- Get users who favorited a film from everyone  
   `/api/film/{tmdb_id}/favorites/from/all`  
- Get users who favorited a film from following  
   `/api/film/{tmdb_id}/favorites/from/following`  
- Get user favorite films  
   `/api/user/{user_id}/favorites`  
- Get film metadata  
   `/api/film/{tmdb_id}`  
- Get self film status  
   `/api/film/{tmdb_id}/self`  
- Get user followings  
   `/api/user/{user_id}/followings`  
- Get user followers  
   `/api/user/{user_id}/followers`  
- Get user mutuals  
   `/api/user/{user_id}/mutuals`  
- Get user points  
   `/api/user/{user_id}/points`  
- Get film reviews from everyone  
   `/api/film/{tmdb_id}/reviews/from/all`  
- Get film reviews from following  
   `/api/film/{tmdb_id}/reviews/from/following`  
- Get liked film reviews  
   `/api/film/{tmdb_id}/reviews/liked`  
- Get self film reviews  
   `/api/film/{tmdb_id}/reviews/self`  
- Get user reviews  
   `/api/user/{user_id}/reviews`  
- Get review detail  
   `/api/review/{id}`  
- Get reviews from everyone  
   `/api/reviews`  
- Get reviews from following  
   `/api/reviews/timeline`  
- Get users who liked a review from everyone  
   `/api/review/{review_id}/likes/from/all`  
- Get users who liked a review from following  
   `/api/review/{review_id}/likes/from/following`  
- Get specific users  
   `/api/user/search/{query}`  
- Get user detail  
   `/api/user/{id}`  
- Get self detail  
   `/api/user/self`  
- Get users who watchlisted a film from everyone  
   `/api/film/{tmdb_id}/watchlists/from/all`  
- Get users who watchlisted a film from following  
   `/api/film/{tmdb_id}/watchlists/from/following`  
- Get users watchlist films  
   `/api/user/{user_id}/watchlists`  
- Get status code meaning  
   `/developer/status`  

### POST
- Sign up user  
   `/api/user/signup`  
- Sign in user  
   `/api/user/signin`  
- Create comment  
   `/api/comment`  
- Like comment  
   `/api/comment/{comment_id}/like`  
- Add film to favorite  
   `/api/film/{tmdb_id}/favorite`  
- Follow user  
   `/api/user/{user_id}/follow`  
- Create review  
   `/api/review`  
- Like review  
   `/api/review/{review_id}/like`  
- Add film to watchlist  
   `/api/film/{tmdb_id}/watchlist`  
- Sign up developer  
   `/developer/signup`  

### PUT
- Edit review  
   `/api/review/{id}`  
- Edit user profile  
   `/api/user/update/profile`  
- Edit user password  
   `/api/user/update/password`  

### DELETE
- Delete comment  
   `/api/comment/{id}`  
- Unlike comment  
   `/api/comment/{comment_id}/unlike`  
- Delete film from favorite  
   `/api/film/{tmdb_id}/unfavorite`  
- Unfollow user  
   `/api/user/{user_id}/unfollow`  
- Delete review  
   `/api/review/{id}`  
- Unlike review  
   `/api/review/{review_id}/unlike`  
- Delete film from watchlist  
   `/api/film/{tmdb_id}/unwatchlist`  

## Changelog
### 1.1.0 (Ongoing)
- Adding like comment feature
- Adding point feature
- Showing total point on user detail
### 1.0.0
- Firing up initial version of Popularin API!
