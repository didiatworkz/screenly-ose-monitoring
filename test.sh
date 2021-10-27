DOCK_ID=$(sudo docker ps -q -f name=somo)
if [ -n "$DOCK_ID" ]; then
    echo -e "[ \e[33mSOMO\e[39m ] Old SOMO version found (docker)"
    # Save old port
    PORT=$(sudo docker container port $DOCK_ID | awk '{print $1}' | sed s'/\/tcp//')
    echo -e "[ \e[33mSOMO\e[39m ] Stop and remove container..."
    sudo docker stop $DOCK_ID
    #sudo docker rm $DOCK_ID
    echo -e "[ \e[33mSOMO\e[39m ] Container stopped and removed!"
    BACKUP_C2=1
fi

echo "Port: $PORT"
