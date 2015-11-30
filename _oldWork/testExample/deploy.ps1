$exempleName = "testexample"

$theLocation = "West Europe"
$theName = $("testAzureRessource"+$exempleName+"RG")
$theDeploymentName = $("testAzureRessource"+$exempleName+"DN")
$templateFile = "azuredeploy.json"

#Switch-AzureMode AzureResourceManager
New-AzureResourceGroup -Location $theLocation -Name $theName -DeploymentName $theDeploymentName -TemplateFile $templateFile


