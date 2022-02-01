touch /tmp/dependancy_JeeMySensors_in_progress
echo 0 > /tmp/dependancy_JeeMySensors_in_progress
echo "********************************************************"
echo "*             Installation des dépendances             *"
echo "********************************************************"
apt-get update
echo 50 > /tmp/dependancy_JeeMySensors_in_progress
apt-get install -y python3-serial python3-requests python3-pyudev
echo 100 > /tmp/dependancy_JeeMySensors_in_progress
echo "********************************************************"
echo "*             Installation terminée                    *"
echo "********************************************************"
rm /tmp/dependancy_JeeMySensors_in_progress
