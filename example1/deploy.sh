azure config mode arm
#azure group create -n "testsimonhakfestexample1" -l "West US"

azure group create -n "testsimonhakfestexample1" -l "West US" --template-file azuredeploy.json --deployment-name "testDeployExample1" --parameters-file azuredeploy.parameters.json
azure group deployment show "testRG" "testDeployExample1"

testhackfest@office365.brainsonic.com
Puka8300