#!/bin/bash
# This script updates the package lists and installs specified packages.
echo "Executing initial-script.sh"
set -e

# Ensure the script is run as root
if [ "$(id -u)" != "0" ]; then
    echo "This script must be run as root" 1>&2
    exit 1
fi

# Update package lists
echo "Updating package lists..."
apt-get update

echo "Installing packages..."
# Install necessary packages
apt-get install -y \
    iputils-ping \
    curl \
    telnet \
    net-tools \
    wget \
    gnupg2 \
    software-properties-common \
    autoconf \
    automake \
    libtool \
    build-essential \
    php-pear \
    php-dev \
    librdkafka-dev

# Check if confluent-platform is already installed
#if ! dpkg -s confluent-platform >/dev/null 2>&1; then
#    echo "Downloading confluent... adding key"
#    wget -qO - https://packages.confluent.io/deb/7.6/archive.key | gpg --dearmor -o /usr/share/keyrings/confluent-archive-keyring.gpg
#
#    echo "Adding repositories..."
#    echo "deb [signed-by=/usr/share/keyrings/confluent-archive-keyring.gpg] https://packages.confluent.io/deb/7.6 stable main" >/etc/apt/sources.list.d/confluent.list
#    echo "deb [signed-by=/usr/share/keyrings/confluent-archive-keyring.gpg] https://packages.confluent.io/clients/deb $(lsb_release -cs) main" >>/etc/apt/sources.list.d/confluent.list

#    echo "Updating..."
#    apt-get update

#    #echo "confluent-platform not installed. Installing..."
#    #apt-get install -y confluent-platform
#else
#    echo "confluent-platform is already installed."
#fi

# Clear PECL cache if it exists
echo "Clearing cache..."
if [ -d "/tmp/pear/cache" ]; then
    pecl clear-cache
else
    mkdir -p /tmp/pear/cache
    echo "Cache directory created at /tmp/pear/cache"
fi

# Update PECL channel
pecl channel-update pecl.php.net

echo "Installing rdkafka..."
if pecl list | grep -qi '^rdkafka'; then
    echo "rdkafka is already installed."
else
    echo "rdkafka is not installed. Installing..."
    yes '' | pecl install rdkafka
    echo "extension=rdkafka.so" >> /etc/php/8.3/cli/php.ini
fi

echo "Initial script completed successfully."
