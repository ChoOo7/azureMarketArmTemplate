$exempleName = "example108"
$appName = $("hackfest"+$exempleName)


$theLocation = "West Europe"
$theName = $("testAzureRessource"+$exempleName+"RG")
$theDeploymentName = $("testAzureRessource"+$exempleName+"DN")
$templateFile = "azuredeploy.json"
$templateParametersFile = "azuredeploy.parameters.json"
    
Switch-AzureMode AzureResourceManager
New-AzureResourceGroup -Location $theLocation -Name $theName
New-AzureResourceGroupDeployment -Name $theDeploymentName -ResourceGroupName $theName -TemplateFile $templateFile -appName $appName -Verbose


#Set-AzureVM -ResourceGroupName testAzureRessourceexample107RG -Name imghelpervm -Generalized
#Save-AzureVMImage -ResourceGroupName testAzureRessourceexample107RG -VMName imghelpervm -DestinationContainerName vhds -VHDNamePrefix "myimage"
#http://hackfestexample107bsvhd.blob.core.windows.net/system/Microsoft.Compute/Images/vhds/myimage-osDisk.863b918c-e78a-423d-aac4-493125488624.vhd

