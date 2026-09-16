## About This Project
At a midpoint in my expressPay internship, I was tasked with building a project of any kind, 
provided that it utilized one of the Firm's APIs. After a bit of brainstorming, I landed on this
simple game inspired by Rock Paper Scissors, where you can conveniently buy more lives with the
Tokenization API. Security wasn't a priority in this project like it was in my subsequent final project.
## How it work
- Enter in login details as these are required for the Tokenization API to work. These are saved in the server session until reset.
- Then you just select your weapon and fight against the 15 enemies. It's designed so that you're likely to die at least twice before
  beating all 15 enemies, though if you get through unscathed congrats! 
- When you eventually die for the first time, you will be prompted to buy more lives via expressPay. After entering your card details
  you will be sent back with an extra three lives. The next time you die, you can simply pay for the new lives with one click.
- The Tokenization API creates a unique cctoken when new card details are inputed. My program saves this unique cctoken as a cookie in
  the browser and so it can facilitate automatic payment without redirected to expressPay to re-enter card details.

  

## Screenshot
![image](https://github.com/Cosmonations/ADVENTURE-BROS/blob/main/Screenshot%20From%202026-09-15%2017-11-01.png)
![image](https://github.com/Cosmonations/ADVENTURE-BROS/blob/main/Screenshot%20From%202026-09-15%2017-11-11.png)
![image](https://github.com/Cosmonations/ADVENTURE-BROS/blob/main/Screenshot%20From%202026-09-15%2020-56-08.png)
![image](https://github.com/Cosmonations/ADVENTURE-BROS/blob/main/Screenshot%20From%202026-09-15%2021-15-58.png)
![image](https://github.com/Cosmonations/ADVENTURE-BROS/blob/main/Screenshot%20From%202026-09-15%2021-16-26.png)

