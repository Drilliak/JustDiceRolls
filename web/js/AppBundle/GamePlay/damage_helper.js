/**
 *
 * @param additionMax nombre maximum à rajouter au lancer de dé (ex : 2d6 + additionMax)
 * @param nbDicesMax nombre maximum de dés à lancer (ex : nbDicesMaxd1 + additionMax)
 * @returns {{}}
 */
let createDiceRollsObject = function (additionMax, nbDicesMax) {
    let diceRolls = {};
    let avg1Dice;
    let max1Dice;

    // Cas des dés 4
    avg1Dice = 2.5;
    max1Dice = 4;
    for (let nbDices = 1; nbDices <= nbDicesMax; nbDices++) {
        for (let addition = 0; addition <= additionMax; addition++) {
            let moy = nbDices * avg1Dice + addition;
            let max = nbDices * max1Dice + addition;
            let dice = nbDices + "d4";
            if (addition != 0) {
                dice += "+" + addition;
            }
            diceRolls[dice] = {"moy": moy, "max": max};
        }
    }

    // Cas des dés 6
    avg1Dice = 3.5;
    max1Dice = 6;
    for (let nbDices = 1; nbDices <= nbDicesMax; nbDices++) {
        for (let addition = 0; addition <= additionMax; addition++) {
            let moy = nbDices * avg1Dice + addition;
            let max = nbDices * max1Dice + addition;
            let dice = nbDices + "d6";
            if (addition != 0) {
                dice += "+" + addition;
            }
            diceRolls[dice] = {"moy": moy, "max": max};
        }
    }

    return diceRolls;
};

let diceRolls = createDiceRollsObject(15,15);

(function(){
    $('.spells').on('input', '#value-dice-player', function(){
       let nbSteps = 5;
       let maxDice = parseInt($('.spell-data #max-value-success').val());
       let repartition = new Array(nbSteps).fill(1);

       let sum = nbSteps;
       let indexMaxElement = -1;
       while(sum < maxDice -2){
           if (indexMaxElement < repartition.length -1){
               indexMaxElement++;
               repartition[indexMaxElement]++;
           } else {
               indexMaxElement = 0;
               repartition[0]++;
           }
           sum++;
       }
       let paliers = [];
       paliers[0] = 1;
       for (let i = 0; i <repartition.length; i++){
           paliers[i+1] = paliers[i] + repartition[i];
       }

        let almostSuccessMax = parseInt($("#almost-success-max").val());
        let almostSuccessAvg = parseInt($("#almost-success-avg").val());
        let almostFailureMax = parseInt($("#almost-failure-max").val());
        let almostFailureAvg = parseInt($("#almost-failure-avg").val());
        let maxsAndAvg = [];

        for (let i =0; i <nbSteps; i++){
            let max = i * (almostSuccessMax - almostSuccessAvg)/(nbSteps-1) + almostSuccessAvg;
            let avg = i * (almostFailureMax - almostFailureAvg)/(nbSteps-1) + almostFailureAvg;

            let distanceMax = Number.MAX_VALUE;
            let distanceMoy = Number.MAX_VALUE;
            let roll = "";
            for (let diceRoll in diceRolls){

                let tempMax = diceRolls[diceRoll].max;
                let tempMoy = diceRolls[diceRoll].moy;

                if (Math.abs(tempMax-max) <= distanceMax && Math.abs(tempMoy-avg) <= distanceMoy){
                    distanceMax = Math.abs(tempMax-max);
                    distanceMoy = Math.abs(tempMoy-avg);
                    roll = diceRoll;
                }
            }
            let res = {"max":max, "avg":avg, "roll":roll};
            maxsAndAvg.push(res);
        }
        console.log(paliers);
        maxsAndAvg.reverse();
        let dicePlayerValue = parseInt($('#value-dice-player').val());
        for (let i =0; i<maxsAndAvg.length; i++){
            if (dicePlayerValue > paliers[i] && dicePlayerValue <= paliers[i+1]){
                $('#damage-dice').text(maxsAndAvg[i].roll);
                console.log(maxsAndAvg[i].roll);
                break;
            }
        }
    });
})();