
Switch-AzureMode AzureServiceManagement
#Save-AzureVMImage  -Name "" -VMName "hackfestexample74-front1" -DestinationContainerName "vhds" -VHDNamePrefix "testtemplate1" -Name "testtemplate1name"


Save-AzureVMImage -ServiceName "testazurebstpl2" -Name "testazurebstpl2" -OSState "Generalized" -ImageName "myAwesomeVMImage1" -ImageLabel "This is my 1 Virtual Machine Image" -Verbose