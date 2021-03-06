echo This computer: %computername%
rem disconnect the wireless
netsh interface set interface "Wireless Network Connection" admin=disable 

netsh interface ipv4 show config name="Local Area Connection"
netsh interface ipv4 set address name="Local Area Connection" static 10.52.20.30 255.255.255.0 10.52.20.1
netsh interface ipv4 set dnsserver name="Local Area Connection" static 10.52.128.10 primary
netsh interface ipv4 add dnsservers name="Local Area Connection" address="137.201.128.0" validate=yes index=2

echo current dir = %cd%

@echo MSGBOX "Wired connection at Micron is configured for static IP 10.52.20.30" > TEMPmessage.vbs
@call TEMPmessage.vbs
@del TEMPmessage.vbs /f /q
